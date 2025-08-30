<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Test Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-800 text-white p-8">

<div class="container mx-auto">
    <h1 class="text-3xl font-bold mb-6">Host Test Page</h1>

    <div class="grid grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl font-semibold mb-4">Fire Host Events</h2>
            <div class="space-y-4">
                <button onclick="fireEvent('start')" class="bg-green-500 w-full p-4 rounded hover:bg-green-600">Start Game</button>
                <button onclick="fireEvent('next')" class="bg-blue-500 w-full p-4 rounded hover:bg-blue-600">Next Question</button>
                <button onclick="fireEvent('end')" class="bg-red-500 w-full p-4 rounded hover:bg-red-600">End Game</button>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-4">Received Events Log</h2>
            <div id="log" class="bg-gray-900 p-4 rounded-lg h-96 overflow-y-auto">
                <!-- Log messages will appear here -->
            </div>
        </div>
    </div>
</div>

<script>
    const sessionCode = 'TEST123'; // Hardcoded for testing

    async function fireEvent(eventName) {
        await fetch(`/test/fire/${eventName}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ session_code: sessionCode })
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const log = document.getElementById('log');

        function addLog(message) {
            const el = document.createElement('div');
            el.className = 'border-b border-gray-700 p-2';
            el.innerText = `[${new Date().toLocaleTimeString()}] ${message}`;
            log.prepend(el);
        }

        window.Echo.channel('jalsah.session.' + sessionCode)
            .listen('.PlayerJoined', (e) => {
                addLog(`Player Joined: ${e.player.nickname}`);
            })
            .listen('.AnswerSubmitted', (e) => {
                addLog(`Answer Submitted by ${e.player.nickname}: ${e.answer}`);
            })
            .listen('.PlayerLeft', (e) => {
                addLog(`Player Left: ${e.player.nickname}`);
            });

        addLog('Listening for player events on channel: jalsah.session.' + sessionCode);
    });
</script>

</body>
</html>
