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

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Games</h2>
            <a href="{{ route('admin.games.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create New Game</a>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6">
            <form method="GET" action="{{ route('admin.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by name or description..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <select name="sort_by" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="created_at" {{ ($sortBy ?? 'created_at') === 'created_at' ? 'selected' : '' }}>Sort by Created Date</option>
                        <option value="updated_at" {{ ($sortBy ?? 'created_at') === 'updated_at' ? 'selected' : '' }}>Sort by Updated Date</option>
                        <option value="name" {{ ($sortBy ?? 'created_at') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                    </select>
                    <select name="sort_direction" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="desc" {{ ($sortDirection ?? 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ ($sortDirection ?? 'desc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Search & Filter
                    </button>
                </div>
                @if($search || ($sortBy !== 'created_at') || ($sortDirection !== 'desc'))
                    <a href="{{ route('admin.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Results Summary -->
        <div class="mb-4 text-sm text-gray-600">
            Showing {{ $games->count() }} of {{ $games->total() }} games
            @if($search)
                (filtered by: "{{ $search }}")
            @endif
        </div>

        <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b font-medium text-left">id</th>
                    <th class="py-3 px-4 border-b font-medium text-left">Name</th>
                    <th class="py-3 px-4 border-b font-medium text-left">Description</th>
                    <th class="py-3 px-4 border-b font-medium text-center">Questions</th>
                    <th class="py-3 px-4 border-b font-medium text-center">Status</th>
                    <th class="py-3 px-4 border-b font-medium text-center">Created/Updated</th>
                    <th class="py-3 px-4 border-b font-medium text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($games as $game)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b">{{ $game->id }}</td>
                        <td class="py-3 px-4 border-b">
                            <div>
                                <div class="font-medium">{{ $game->name }}</div>
                                @if($game->creator)
                                    <div class="text-sm text-gray-500">By {{ $game->creator->name }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 border-b">
                            <div class="max-w-xs truncate" title="{{ $game->description }}">
                                {{ $game->description ?? 'No description' }}
                            </div>
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                                {{ $game->questions->count() }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                            @if($game->hasActiveSessions())
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    Active
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b text-center text-sm">
                            <div class="text-gray-600">Created: {{ $game->created_at->format('M j, Y') }}</div>
                            @if($game->updated_at != $game->created_at)
                                <div class="text-gray-500">Updated: {{ $game->updated_at->format('M j, Y') }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.games.edit', $game->id) }}"
                                   class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded text-sm bg-blue-50 hover:bg-blue-100 transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this game? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            @if($game->hasActiveSessions()) disabled
                                            title="Cannot delete game with active sessions"
                                            class="bg-gray-100 text-gray-400 px-3 py-1 rounded text-sm cursor-not-allowed"
                                            @else
                                            class="text-red-600 hover:text-red-800 px-3 py-1 rounded text-sm bg-red-50 hover:bg-red-100 transition-colors"
                                            @endif>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 px-4 border-b text-center text-gray-500">
                            @if($search)
                                No games found matching your search criteria.
                            @else
                                No games created yet. <a href="{{ route('admin.games.create') }}" class="text-blue-600 hover:underline">Create your first game</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($games->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $games->onEachSide(2)->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

</body>
</html>
