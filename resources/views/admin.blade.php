<!doctype html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin - Jalsah</title>
</head>
<body>
  <h1>Admin Dashboard</h1>
  <button id="start">Start Session</button>
  <button id="finish">Finish Session</button>
  <script>
    async function post(path){
      const res = await fetch(path, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
      });
      return res.json();
    }
    document.getElementById('start').onclick = () => post('/api/sessions/JALSAH123/start').then(r => alert(JSON.stringify(r)));
    document.getElementById('finish').onclick = () => post('/api/sessions/JALSAH123/finish').then(r => alert(JSON.stringify(r)));
  </script>
</body>
</html>
