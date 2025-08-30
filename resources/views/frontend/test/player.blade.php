<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Test Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-700 text-white p-8">

<div class="container mx-auto">
    <h1 class="text-3xl font-bold mb-6">Player Test Page</h1>

    <div class="grid grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl font-semibold mb-4">Fire Player Events</h2>
            <div class="space-y-4">
                <button onclick="fireEvent('join')" class="bg-purple-500 w-full p-4 rounded hover:bg-purple-600">Join Game</button>
                <button onclick="fireEvent('answer')" class="bg-indigo-500 w-full p-4 rounded hover:bg-indigo-600">Select Answer</button>
                <button onclick="fireEvent('leave')" class="bg-pink-500 w-full p-4 rounded hover:bg-pink-600">Exit Game</button>
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
            body: JSON.stringify({ session_code: sessionCode, nickname: 'TestPlayer', answer: 'A' })
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const log = document.getElementById('log');

        function addLog(message) {
            const el = document.createElement('div');
            el.className = 'border-b border-gray-600 p-2';
            el.innerText = `[${new Date().toLocaleTimeString()}] ${message}`;
            log.prepend(el);
        }

        window.Echo.channel('jalsah.session.' + sessionCode)
            .listen('.SessionStarted', (e) => {
                addLog('Event Received: Session Started');
            })
            .listen('.NextQuestion', (e) => {
                addLog(`Event Received: Next Question (Index: ${e.questionIndex})`);
            })
            .listen('.SessionFinished', (e) => {
                addLog('Event Received: Session Finished');
            });

        addLog('Listening for host events on channel: jalsah.session.' + sessionCode);
    });
</script>

</body>
</html>
