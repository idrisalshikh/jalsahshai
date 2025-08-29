<!doctype html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Play - Jalsah</title>
  @vite('resources/js/app.js')
</head>
<body>
  <div id="join">
    Nickname: <input id="nick" /> <button id="joinBtn">Join</button>
  </div>

  <div id="waiting" style="display:none;">
    Waiting for session to start...
  </div>

  <div id="videoWrap" style="display:none;">
    <video id="jvideo" width="640" controls></video>
  </div>

  <div id="questionWrap" style="display:none;">
    <div id="qText"></div>
    <div id="qOptions"></div>
    <button id="nextBtn" style="display:none;">Next</button>
  </div>

  <div id="result" style="display:none;"></div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const code = 'JALSAH123';
    let session = null;
    let nickname = null;
    let currentIndex = 0;

    document.getElementById('joinBtn').onclick = async () => {
      nickname = document.getElementById('nick').value || 'guest';
      document.getElementById('join').style.display = 'none';
      document.getElementById('waiting').style.display = 'block';
      // fetch session
      const res = await fetch('/api/sessions/' + code);
      session = await res.json();

      // subscribe to Echo
      window.Echo.channel('jalsah.' + code)
        .listen('.SessionStarted', (data) => { // Note the leading dot
          startVideo(data.session);
        })
        .listen('.SessionFinished', (data) => { // Note the leading dot
          showResult();
        });

      // if session already started (cache), start immediately
      if (session.status === 'started') startVideo(session);
    };

    function startVideo(s){
      session = s;
      document.getElementById('waiting').style.display = 'none';
      document.getElementById('videoWrap').style.display = 'block';
      const v = document.getElementById('jvideo');
      v.src = session.video_url;
      v.play().catch(()=>{});
      v.onended = () => {
        document.getElementById('videoWrap').style.display = 'none';
        showQuestion(0);
      };
    }

    function showQuestion(index){
      currentIndex = index;
      const q = session.questions[index];
      const wrap = document.getElementById('questionWrap');
      wrap.style.display = 'block';
      document.getElementById('qText').innerText = q.text;
      const opts = document.getElementById('qOptions');
      opts.innerHTML = '';
      if (q.type === 'mcq') {
        q.options.forEach((opt,i) => {
          const btn = document.createElement('button');
          btn.innerText = opt;
          btn.onclick = () => submitAnswer(index, i);
          opts.appendChild(btn);
        });
      } else if (q.type === 'true_false') {
        const t = document.createElement('button');
        t.innerText = '✅ True';
        t.onclick = () => submitAnswer(index, true);
        const f = document.createElement('button');
        f.innerText = '❌ False';
        f.onclick = () => submitAnswer(index, false);
        opts.appendChild(t); opts.appendChild(f);
      }
    }

    async function submitAnswer(index, answer){
      // log to server
      await fetch('/api/sessions/' + code + '/answer', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({nickname, question_index: index, answer})
      });
      // next question or result
      document.getElementById('questionWrap').style.display = 'none';
      if (index + 1 < session.questions.length) {
        showQuestion(index + 1);
      } else {
        showResult();
      }
    }

    function showResult(){
      document.getElementById('result').style.display = 'block';
      document.getElementById('result').innerText = 'Thank you! Results will be available in the admin panel (prototype).';
    }
  });
</script>
</body>
</html>
