<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Waiting Room - {{ $session->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <h1 class="text-4xl font-bold mb-4">Hosting: {{ $session->game->name }}</h1>
    <p class="text-2xl text-gray-700 mb-6">Session Code: <span class="font-bold text-blue-500">{{ $session->code }}</span></p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl font-semibold mb-4">Waiting for Players...</h2>
            <div id="players-list" class="bg-white p-6 rounded-lg shadow-md">
                <!-- Player list will be updated here in real-time -->
            </div>
        </div>
        <div>
            @if($session->game->video_url)
                <div class="aspect-video bg-black rounded-lg overflow-hidden">
                    <iframe
                        width="100%"
                        height="100%"
                        src="https://www.youtube.com/embed/{{ $session->game->video_url }}"
                        title="Intro Video"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            @else
                <div class="aspect-video bg-gray-200 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500 text-lg">No intro video available</p>
                </div>
            @endif
            <form action="{{ route('host.start', $session->code) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="bg-green-500 text-white w-full px-6 py-3 rounded hover:bg-green-600">Start Game</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sessionCode = @json($session->code);
        const playersList = document.getElementById('players-list');

        window.Echo.channel('jalsah.session.' + sessionCode)
            .listen('.PlayerJoined', (e) => {
                const playerEl = document.createElement('div');
                playerEl.innerText = e.player.nickname;
                playersList.appendChild(playerEl);
            });
    });
</script>

</body>
</html>
