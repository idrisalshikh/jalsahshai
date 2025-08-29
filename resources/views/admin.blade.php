<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div id="app" class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Game Sessions</h2>
            <button @click="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create New Session</button>
        </div>

        <ul>
            <li v-for="session in sessions" :key="session.id" class="border-b py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">{{ session.code }}</h3>
                    <p class="text-gray-600">{{ session.questions.length }} questions</p>
                    <p class="text-sm text-gray-500">Status: {{ session.status }}</p>
                </div>
                <div>
                    <button @click="startSession(session)" class="bg-green-500 text-white px-3 py-1 rounded mr-2 hover:bg-green-600">Start</button>
                    <button @click="finishSession(session)" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600">Finish</button>
                    <button @click="openModal(session)" class="bg-gray-500 text-white px-3 py-1 rounded mr-2 hover:bg-gray-600">Edit</button>
                    <button @click="deleteSession(session)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
                </div>
            </li>
        </ul>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
            <h2 class="text-2xl font-bold mb-6">{{ editingSession ? 'Edit' : 'Create' }} Session</h2>
            <form @submit.prevent="saveSession">
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700">Session Code</label>
                    <input type="text" v-model="form.code" id="code" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                    <input type="text" v-model="form.video_url" id="video_url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <h3 class="text-xl font-semibold mb-4 mt-6">Questions</h3>
                <div v-for="(question, index) in form.questions" :key="index" class="border p-4 rounded-md mb-4">
                    <div class="flex justify-end">
                        <button type="button" @click="removeQuestion(index)" class="text-red-500 hover:text-red-700">Remove</button>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" v-model="question.text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select v-model="question.type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>
                    <div v-if="question.type === 'mcq'" class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Options (comma-separated)</label>
                        <input type="text" :value="question.options.join(',')" @input="question.options = $event.target.value.split(',')" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                        <input type="text" v-model="question.correct_answer" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                         <p class="text-xs text-gray-500">For MCQ, provide the index (0-based). For True/False, use 'true' or 'false'.</p>
                    </div>
                </div>
                <button type="button" @click="addQuestion" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Add Question</button>

                <div class="mt-8 flex justify-end">
                    <button type="button" @click="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded mr-2 hover:bg-gray-500">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const sessions = ref([]);
            const showModal = ref(false);
            const editingSession = ref(null);
            const form = ref({
                code: '',
                video_url: '',
                questions: []
            });

            const fetchSessions = async () => {
                const response = await fetch('/api/admin/sessions');
                sessions.value = await response.json();
            };

            const openModal = (session = null) => {
                if (session) {
                    editingSession.value = session;
                    form.value = JSON.parse(JSON.stringify(session)); // Deep copy
                } else {
                    editingSession.value = null;
                    form.value = { code: '', video_url: '', questions: [] };
                }
                showModal.value = true;
            };

            const closeModal = () => {
                showModal.value = false;
            };

            const addQuestion = () => {
                form.value.questions.push({ text: '', type: 'mcq', options: [], correct_answer: '' });
            };

            const removeQuestion = (index) => {
                form.value.questions.splice(index, 1);
            };

            const saveSession = async () => {
                const method = editingSession.value ? 'PUT' : 'POST';
                const url = editingSession.value ? `/api/admin/sessions/${editingSession.value.id}` : '/api/admin/sessions';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(form.value)
                });

                if (response.ok) {
                    await fetchSessions();
                    closeModal();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + JSON.stringify(errorData.errors));
                }
            };

            const deleteSession = async (session) => {
                if (confirm(`Are you sure you want to delete session ${session.code}?`)) {
                    const response = await fetch(`/api/admin/sessions/${session.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });

                    if (response.ok) {
                        await fetchSessions();
                    } else {
                        alert('Error deleting session.');
                    }
                }
            };

            const startSession = async (session) => {
                await fetch(`/api/sessions/${session.code}/start`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                await fetchSessions();

            };

            const finishSession = async (session) => {
                await fetch(`/api/sessions/${session.code}/finish`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                await fetchSessions();

            };

            onMounted(fetchSessions);

            return {
                sessions,
                showModal,
                editingSession,
                form,
                fetchSessions,
                openModal,
                closeModal,
                addQuestion,
                removeQuestion,
                saveSession,
                deleteSession,
                startSession,
                finishSession,
            };
        }
    }).mount('#app');
</script>

</body>
</html>
