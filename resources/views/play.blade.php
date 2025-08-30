<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Play - {{ $session->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-2">Playing: {{ $session->code }}</h1>
    <p class="mb-6">Welcome, {{ $nickname }}!</p>

    <div id="waiting" class="bg-white p-6 rounded-lg shadow-md text-center" style="{{ $session->status !== 'waiting' ? 'display:none;' : '' }}">
        <p class="text-xl">Waiting for the session to start...</p>
    </div>

    <div id="videoWrap" class="bg-white p-6 rounded-lg shadow-md" style="{{ $session->status !== 'started' ? 'display:none;' : '' }}">
        <video id="jvideo" width="100%" controls src="{{ $session->video_url }}"></video>
    </div>

    <div id="questionWrap" class="bg-white p-6 rounded-lg shadow-md" style="display:none;">
        <h2 id="qText" class="text-2xl font-bold mb-4"></h2>
        <div id="qOptions" class="grid grid-cols-2 gap-4"></div>
    </div>

    <div id="result" class="bg-white p-6 rounded-lg shadow-md text-center" style="display:none;">
        <p class="text-xl">Thank you for playing! The session has finished.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const session = @json($session);
        const nickname = @json($nickname);
        let currentIndex = 0;

        const waitingDiv = document.getElementById('waiting');
        const videoWrap = document.getElementById('videoWrap');
        const questionWrap = document.getElementById('questionWrap');
        const resultDiv = document.getElementById('result');
        const video = document.getElementById('jvideo');

        // Subscribe to Echo for real-time events
        window.Echo.channel('jalsah.' + session.code)
            .listen('.SessionStarted', (data) => {
                startVideo(data.session);
            })
            .listen('.SessionFinished', (data) => {
                showResult();
            });

        function startVideo(s) {
            waitingDiv.style.display = 'none';
            videoWrap.style.display = 'block';
            video.play().catch(() => {});
            video.onended = () => {
                videoWrap.style.display = 'none';
                showQuestion(0);
            };
        }

        function showQuestion(index) {
            currentIndex = index;
            const q = session.questions[index];
            questionWrap.style.display = 'block';
            document.getElementById('qText').innerText = q.text;
            const opts = document.getElementById('qOptions');
            opts.innerHTML = '';

            if (q.type === 'mcq') {
                q.options.forEach((opt, i) => {
                    const btn = document.createElement('button');
                    btn.innerText = opt;
                    btn.className = 'bg-blue-500 text-white p-4 rounded hover:bg-blue-600';
                    btn.onclick = () => submitAnswer(index, i);
                    opts.appendChild(btn);
                });
            } else if (q.type === 'true_false') {
                const t = document.createElement('button');
                t.innerText = '✅ True';
                t.className = 'bg-green-500 text-white p-4 rounded hover:bg-green-600';
                t.onclick = () => submitAnswer(index, 'true');
                opts.appendChild(t);
                const f = document.createElement('button');
                f.innerText = '❌ False';
                f.className = 'bg-red-500 text-white p-4 rounded hover:bg-red-600';
                f.onclick = () => submitAnswer(index, 'false');
                opts.appendChild(f);
            }
        }

        async function submitAnswer(index, answer) {
            const questionId = session.questions[index].id;
            await fetch(`/play/${session.code}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer: answer,
                    _token: document.querySelector('meta[name=csrf-token]').content
                })
            });

            questionWrap.style.display = 'none';
            if (index + 1 < session.questions.length) {
                showQuestion(index + 1);
            } else {
                showResult();
            }
        }

        function showResult() {
            waitingDiv.style.display = 'none';
            videoWrap.style.display = 'none';
            questionWrap.style.display = 'none';
            resultDiv.style.display = 'block';
        }

        // Initial state
        if (session.status === 'started') {
            startVideo(session);
        } else if (session.status === 'finished') {
            showResult();
        }
    });
</script>

</body>
</html>
