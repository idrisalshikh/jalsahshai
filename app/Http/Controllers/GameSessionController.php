<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Events\SessionStarted;
use App\Events\SessionFinished;

class GameSessionController extends Controller
{
    protected function baseSession()
    {
        return config('jalsah.session');
    }

    protected function runtimeSession()
    {
        $s = $this->baseSession();
        $status = Cache::get('jalsah.status');
        if ($status) {
            $s['status'] = $status;
        }
        return $s;
    }

    public function index()
    {
        return response()->json([$this->runtimeSession()]);
    }

    public function show($code)
    {
        $s = $this->runtimeSession();
        if ($s['code'] !== $code) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json($s);
    }

    public function start($code)
    {
        $s = $this->baseSession();
        if ($s['code'] !== $code) {
            return response()->json(['error' => 'Not found'], 404);
        }
        Cache::put('jalsah.status', 'started', now()->addHours(6));
        event(new SessionStarted($this->runtimeSession()));
        return response()->json(['status' => 'started']);
    }

    public function finish($code)
    {
        $s = $this->baseSession();
        if ($s['code'] !== $code) {
            return response()->json(['error' => 'Not found'], 404);
        }
        Cache::put('jalsah.status', 'finished', now()->addHours(6));
        event(new SessionFinished($this->runtimeSession()));
        return response()->json(['status' => 'finished']);
    }

    // Simple logging endpoint for answers (no DB)
    public function answer(Request $request, $code)
    {
        $payload = [
            'code' => $code,
            'nickname' => $request->input('nickname'),
            'question_index' => $request->input('question_index'),
            'answer' => $request->input('answer'),
            'at' => now()->toDateTimeString(),
        ];
        Log::info('Jalsah answer', $payload);
        return response()->json(['logged' => true]);
    }
}
