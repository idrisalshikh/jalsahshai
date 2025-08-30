<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Play - {{ $session->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Hosting: {{ $session->game->name }}</h1>

    <div id="questionWrap" class="bg-white p-6 rounded-lg shadow-md">
        <h2 id="qText" class="text-2xl font-bold mb-4"></h2>
        <div id="qOptions" class="grid grid-cols-2 gap-4 mb-6"></div>
        <div id="answers-list" class="border-t pt-4">
            <h3 class="text-xl font-semibold mb-2">Answers:</h3>
            <!-- Answers will be updated here in real-time -->
        </div>
        <form action="{{ route('host.next', $session->code) }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="bg-blue-500 text-white w-full px-6 py-3 rounded hover:bg-blue-600">Next Question</button>
        </form>
    </div>

    <div id="scoreboard" class="bg-white p-6 rounded-lg shadow-md" style="display:none;">
        <h2 class="text-2xl font-bold mb-4">Final Scores</h2>
        <div id="scores-list"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const session = @json($session);
        let currentQuestionIndex = 0;

        function showQuestion(index) {
            const q = session.game.questions[index];
            document.getElementById('qText').innerText = q.text;
            const opts = document.getElementById('qOptions');
            opts.innerHTML = '';

            if (q.type === 'mcq') {
                q.options.forEach((opt, i) => {
                    const div = document.createElement('div');
                    div.innerText = `${i}: ${opt}`;
                    div.className = 'p-4 border rounded';
                    opts.appendChild(div);
                });
            } else if (q.type === 'true_false') {
                const t = document.createElement('div');
                t.innerText = '✅ True';
                t.className = 'p-4 border rounded';
                const f = document.createElement('div');
                f.innerText = '❌ False';
                f.className = 'p-4 border rounded';
                opts.appendChild(t);
                opts.appendChild(f);
            }
        }

        showQuestion(currentQuestionIndex);

        function showScores() {
            document.getElementById('questionWrap').style.display = 'none';
            document.getElementById('scoreboard').style.display = 'block';

            fetch(`/api/sessions/${session.code}/scores`)
                .then(res => res.json())
                .then(players => {
                    const scoresList = document.getElementById('scores-list');
                    scoresList.innerHTML = '';
                    players.forEach(player => {
                        const scoreEl = document.createElement('div');
                        scoreEl.className = 'flex justify-between py-2 border-b';
                        scoreEl.innerHTML = `<span>${player.nickname}</span><span>${player.score}</span>`;
                        scoresList.appendChild(scoreEl);
                    });
                });
        }

        // Echo logic to listen for answers will be added here
        window.Echo.channel('jalsah.session.' + session.code)
            .listen('.SessionFinished', (e) => {
                showScores();
            });
    });
</script>

</body>
</html>
