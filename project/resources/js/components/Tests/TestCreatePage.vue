<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-8 rounded-2xl shadow-2xl w-full max-w-4xl text-black dark:text-white">
        <h1 class="text-3xl font-bold mb-6 text-teal-700 dark:text-teal-300">Create a New Test</h1>
        <form @submit.prevent="submitTest">
          <div class="mb-6">
            <label class="block text-lg font-semibold mb-2" for="test-name">Test Name</label>
            <input v-model="testName" id="test-name" type="text" class="w-full border rounded px-3 py-2" required>
          </div>
          <div class="mb-6">
            <label class="block text-lg font-semibold mb-2">Add Questions</label>
            <div class="flex gap-4 mb-4">
              <select v-model="selectedExistingQuestionId" class="border rounded px-2 py-1 bg-gray-100 dark:bg-gray-700">
                <option value="">Select existing question</option>
                <option v-for="q in questions" :key="q.id" :value="q.id">
                  {{ q.text }} ({{ q.type || 'text' }})
                </option>
              </select>
              <button type="button" @click="addExistingQuestion" class="bg-blue-500 text-white px-3 py-1 rounded">Add</button>
            </div>
            <div class="mb-4 flex gap-2">
              <input v-model="newQuestionText" type="text" placeholder="Write your own question..." class="w-2/3 border rounded px-2 py-1">
              <select v-model="newQuestionType" class="border rounded px-2 py-1 bg-gray-100 dark:bg-gray-700">
                <option value="text">Text</option>
                <option value="multiple_choice">Multiple Choice</option>
                <option value="number">Number</option>
              </select>
              <button type="button" @click="addNewQuestion" class="bg-green-600 text-white px-3 py-1 rounded">Add New</button>
            </div>
          </div>
          <div class="mb-6">
            <label class="block text-lg font-semibold mb-2">Questions in This Test</label>
            <div v-if="testQuestions.length === 0" class="text-gray-500">No questions added yet.</div>
            <ul>
              <li v-for="(q, idx) in testQuestions" :key="idx" class="mb-2 flex items-center">
                <span class="flex-1">{{ q.text }} <span class="text-xs text-gray-400">({{ q.type }})</span></span>
                <button type="button" @click="removeQuestion(idx)" class="text-red-600 ml-2">Remove</button>
              </li>
            </ul>
          </div>
          <button type="submit" class="w-full py-3 bg-teal-600 text-white font-bold rounded-lg shadow-md hover:bg-teal-700">Save Test</button>
          <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mt-4">
            {{ success }}
          </div>
          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mt-4">
            {{ error }}
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script>
export default {
  props: {
    questions: {
      type: Array,
      default: () => [],
    }
  },
  data() {
    return {
      testName: '',
      selectedExistingQuestionId: '',
      newQuestionText: '',
      newQuestionType: 'text',
      testQuestions: [],
      success: '',
      error: '',
    };
  },
  methods: {
    addExistingQuestion() {
      const q = this.questions.find(q => q.id == this.selectedExistingQuestionId);
      if (q && !this.testQuestions.some(tq => tq.text === q.text && tq.type === (q.type || 'text'))) {
        // Only copy text and type, do not include id
        this.testQuestions.push({ text: q.text, type: q.type || 'text' });
      }
      this.selectedExistingQuestionId = '';
    },
    addNewQuestion() {
      if (!this.newQuestionText.trim()) return;
      this.testQuestions.push({ text: this.newQuestionText, type: this.newQuestionType });
      this.newQuestionText = '';
      this.newQuestionType = 'text';
    },
    removeQuestion(idx) {
      this.testQuestions.splice(idx, 1);
    },
    async submitTest() {
      this.error = '';
      this.success = '';
      if (!this.testName.trim() || this.testQuestions.length === 0) {
        this.error = 'Test name and at least one question are required.';
        return;
      }
      try {
        const response = await fetch('/tests', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            name: this.testName,
            questions: this.testQuestions,
          }),
        });
        if (!response.ok) {
          const data = await response.json();
          this.error = data.message || 'Failed to create test.';
        } else {
          this.success = 'Test created successfully!';
          this.testName = '';
          this.testQuestions = [];
        }
      } catch (e) {
        this.error = 'An error occurred. Please try again.';
      }
    }
  }
};
</script>