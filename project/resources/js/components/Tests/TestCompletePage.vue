<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-8 rounded-2xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-2xl font-bold text-teal-700 dark:text-teal-300 mb-6 text-center">Test: {{ test?.name }}</h1>
        <form @submit.prevent="submitAnswers" v-if="test && test.questions && test.questions.length">
          <div v-for="question in test.questions" :key="question.id" class="mb-6">
            <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">
              {{ question.text }}
            </label>
            <input
              v-model="answers[question.id]"
              type="text"
              :placeholder="'Your answer...'"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition"
              required
            />
          </div>
          <button
            type="submit"
            class="w-full py-3 bg-teal-500 text-white font-bold rounded-lg shadow-md hover:bg-teal-600 transition dark:bg-teal-700 dark:hover:bg-teal-800"
            :disabled="submitting"
          >
            {{ submitting ? 'Saving...' : 'Save Answers' }}
          </button>
        </form>
        <div v-else class="text-center text-gray-600 dark:text-gray-300">
          No questions found for this test.
          <div class="text-xs mt-2">
            testId: {{ testId }}
          </div>
          <pre class="text-xs mt-4 bg-gray-100 dark:bg-gray-800 p-2 rounded text-left overflow-x-auto text-black dark:text-white">
            {{ test && JSON.stringify(test, null, 2) }}
          </pre>
        </div>
        <div v-if="successMessage" class="mt-4 text-green-600 dark:text-green-400 text-center">
          {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="mt-4 text-red-600 dark:text-red-400 text-center text-black dark:text-white">
          {{ errorMessage || 'No error details.' }}
        </div>
        <div v-if="chartUrl" class="mt-8 flex flex-col items-center">
          <h2 class="text-lg font-bold mb-2 text-teal-700 dark:text-teal-300">Your Result Chart</h2>
          <img
            v-if="chartUrl"
            :src="chartUrl"
            alt="Polar Chart"
            class="max-w-xs rounded shadow-lg border border-gray-200 dark:border-gray-700"
            @error="chartUrl = ''"
          />
          <div v-if="!chartUrl" class="text-red-500 mt-2">
            Chart could not be loaded.
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue';

const testId = window.testId || null;

const test = ref(null);
const answers = ref({});
const submitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const chartUrl = ref('');

onMounted(async () => {
  try {
    // Fetch test and questions
    const response = await fetch(`/api/tests/${testId}`);
    if (response.ok) {
      test.value = await response.json();
      // Initialize answers object
      if (test.value && test.value.questions) {
        for (const q of test.value.questions) {
          answers.value[q.id] = '';
        }
      }
    } else {
      // Show a user-friendly error if 404 or not JSON
      let text = await response.text();
      if (text.includes('404') || text.includes('Not Found')) {
        errorMessage.value = 'Test not found. Please check the link or contact support.';
      } else {
        errorMessage.value = 'Failed to load test. ' + text;
      }
    }
  } catch (e) {
    errorMessage.value = 'Error loading test. ' + (e?.message || e);
  }
});

async function submitAnswers() {
  submitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  chartUrl.value = '';
  try {
    const resp = await fetch('/api/user-answers', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({
        test_id: testId, // Ensure testId is sent for both endpoints
        answers: answers.value
      }),
    });
    if (resp.ok) {
      // After saving answers, send to chart endpoint
      try {
        const chartResp = await fetch('/api/save-polar-chart', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            test_id: testId,
            answers: answers.value
          }),
        });
        if (chartResp.ok) {
          const data = await chartResp.json();
          chartUrl.value = data.chart_url || '';
          successMessage.value = 'Answers and chart saved successfully!';
        } else {
          // Try to get error details from backend
          let err = await chartResp.text();
          errorMessage.value = 'Answers saved, but failed to save chart.' + (err ? ' Details: ' + err : '');
        }
      } catch (e) {
        errorMessage.value = 'Answers saved, but error saving chart. ' + (e?.message || e);
      }
    } else {
      const data = await resp.text();
      errorMessage.value = (data && typeof data === 'string') ? data : 'Failed to save answers.';
    }
  } catch (e) {
    errorMessage.value = 'Error saving answers. ' + (e?.message || e);
  } finally {
    submitting.value = false;
  }
}
</script>
