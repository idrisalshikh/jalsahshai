<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $game->name }} - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-4xl font-bold mb-4">{{ $game->name }}</h1>
        <p class="text-gray-700 mb-6">{{ $game->description }}</p>

        <div class="flex space-x-4">
            <form action="{{ route('host.store') }}" method="POST">
                @csrf
                <input type="hidden" name="game_id" value="{{ $game->id }}">
                <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600">Host Game</button>
            </form>
            {{-- <a href="#" class="bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600">Play Solo</a> --}}
        </div>
    </div>
</div>

</body>
</html>
