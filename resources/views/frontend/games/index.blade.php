<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Lobby - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Game Lobby</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($games as $game)
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-2">{{ $game->name }}</h2>
                <p class="text-gray-600 mb-4">{{ $game->description }}</p>
                <a href="{{ route('games.show', $game->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">View Details</a>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
