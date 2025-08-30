<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Waiting for Game to Start - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold mb-6">Waiting for Host</h1>
        <p class="text-xl text-gray-700">The host has not started the game yet. Please wait.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sessionCode = @json($session->code);

        window.Echo.channel('jalsah.session.' + sessionCode)
            .listen('.SessionStarted', (e) => {
                // When the session starts, reload the page.
                // The server will then serve the correct play view.
                window.location.reload();
            });
    });
</script>

</body>
</html>
