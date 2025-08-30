<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Game Sessions</h2>
            <a href="{{ route('admin.games.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create New Game</a>
        </div>

        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Description</th>
                    <th class="py-2 px-4 border-b">Questions</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($games as $game)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $game->name }}</td>
                        <td class="py-2 px-4 border-b">{{ $game->description }}</td>
                        <td class="py-2 px-4 border-b">{{ $game->questions->count() }}</td>
                        <td class="py-2 px-4 border-b">
                            <a href="{{ route('admin.games.edit', $game->id) }}" class="text-blue-500 hover:underline mr-2">Edit</a>
                            <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
