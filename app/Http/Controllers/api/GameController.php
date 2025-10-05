<?php

namespace App\Http\Controllers\api;

use App\Events\LeaderboardUpdated;
use Illuminate\Http\Request;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\Question;
use App\Events\SessionStarted;
use App\Events\NextQuestion;
use App\Events\PlayerAnswered;
use App\Events\SessionFinished;
use App\Events\ShowAnswers;
use App\Http\Controllers\Controller;
use App\Models\GameSessionQuestion;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function createSession(Request $request)
    {
        //validate game id
         $request->validate([
            'gameId' => 'required|integer|exists:games,id',
        ]);
        $gameId = $request->input('gameId');
        do {
            $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (GameSession::where('code', $code)->exists());
        $session = GameSession::create([
            'game_id' => $gameId, // أو أي معرف لعبة آخر حسب حاجتك
            'code' => $code,
            'status' => 'waiting',
        ]);
            $questions = Question::where('game_id',$gameId)->inRandomOrder()->get()   ;
    foreach ($questions as $q) {
        GameSessionQuestion::create ([
            'game_session_id' => $session->id,
            'question_id' => $q->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
        return response()->json(["code" => $session->code, "id" => $session->id]);
    }
  public function startSession($id)
    {
        
        $session = GameSession::where('code',$id)->firstOrFail();
        $session->status = 'started';
        $session->save();

        broadcast(new SessionStarted($session))->toOthers();

        return response()->json([            
            'id' => $session->code, 
            'status'=>$session->status
        ]);
    }
    public function joinSession($id, Request $request)
    {
         
        // Validate session code
        if (!$id) {
            return response()->json(['error' => 'لابد من ادخال كود الجلسة'], 400);
        }

        // Trim leading zeros and find session
        $session = GameSession::where('code', ltrim($id, '0'))->first();
        if (!$session) {
            return response()->json(['error' => 'الكود المدخل غير موجود'], 404);
        }

        // Check if session is still open for joining
        if ($session->status !== 'waiting') {
            return response()->json(['error' => 'لم يعد من الممكن الأنظمام لهذه الجلسة'], 403);
        }
        $player = Player::create([
            'game_session_id' => $session->id,
            'nickname' => $request->input('name')?$request->input('name'):'Guest'.rand(1000,9999),
            'score' => 0,
        ]);

       
        // Broadcast event
        broadcast(new \App\Events\PlayerJoined($id,$player))->toOthers();

        return response()->json([            
            'id' =>"". $player->id."", 
            'name'=>$player->nickname,
            'score'=>$player->score
        ]);
    }

  

    public function nextQuestion($id)
    {
          Log::info('nextQuestion in session ' . $id);
        $session =  GameSession::where('code',$id)->firstOrFail();
        //check the session status

        $sessionQuestion =GameSessionQuestion::where('game_session_id', $session->id)
        ->whereNull('shown_at')
        ->orderBy('id')
        ->first();

        
        if(!$sessionQuestion)
            return $this->finishSession($id);
        $question=Question::find($sessionQuestion->question_id);
        $sessionQuestion->shown_at=now();
        $sessionQuestion->save();
        broadcast(new NextQuestion($id, $question))->toOthers();
        
        return response()->json([
            'id' => $question->id,
            'text' => $question->text,
            'options' => $question->options
        ]);
    }

    public function submitAnswer($id, Request $request)
    {
        Log::info('submitAnswer in session ' . $id.'request'.json_encode($request->all()));
        $player = Player::findOrFail($request->input('player_id'));
        $question = Question::findOrFail($request->input('question_id'));
        $answer = $request->input('answer_id');

        if ($question->correct_answer == $answer) {
            $player->score += 100;
            $player->save();
        }
        Log::info('question correct_answer_id' . $question->correct_answer . ' in session ' . $id);
        broadcast(new PlayerAnswered($id,$player->id,$player->score,$question->options[$answer]))->toOthers();

        return response()->json(['success' => $question->correct_answer == $answer]);
    }

    public function closeQuestion($id)
    {
        Log::info('closeQuestion in session ' . $id);
           
        $session =  GameSession::where('code',$id)->firstOrFail();
        
        $sessionQuestion = GameSessionQuestion::where('game_session_id', $session->id)
            ->whereNotNull('shown_at')
            ->whereNull('closed_at')
            ->orderBy('shown_at', 'desc')
            ->first();
        if ($sessionQuestion)
        {
            $sessionQuestion->closed_at = now();
            $sessionQuestion->save();
        }

        $players = Player::where('game_session_id', $session->id)->orderByDesc('score')->get();

        broadcast(new LeaderboardUpdated($players,$id))->toOthers();

        return response()->json(["success"=>true,"players"=>$players,"message"=>"Question closed"]);
    }

public function showAnswers($id)
    {
        Log::info('showAnswers in session ' . $id);
           
       

        broadcast(new ShowAnswers($id))->toOthers();

        return response()->json(["success"=>true,"message"=>"showAnswers "]);
    }
    public function finishSession($id)
    {
        Log::info('finishSession in session ' . $id);
        $session =  GameSession::where('code',$id)->firstOrFail();
        $session->status = 'finished';
        $session->save();

        $players = Player::where('game_session_id', $session->id)->orderByDesc('score')->get();

        broadcast(new SessionFinished( $players,$id))->toOthers();

        return response()->json($players);
    }
}
