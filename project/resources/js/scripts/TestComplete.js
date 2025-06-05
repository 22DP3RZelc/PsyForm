import { ref } from 'vue';

const testId = window.testId || null;
const test = ref(null);
const answers = ref({});
const submitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const chartUrl = ref('');

async function fetchTestAndInitAnswers() {
  try {
    const response = await fetch(`/api/tests/${testId}`);
    if (response.ok) {
      test.value = await response.json();
      if (test.value && test.value.questions) {
        for (const q of test.value.questions) {
          answers.value[q.id] = '';
        }
      }
    } else {
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
}

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
        test_id: testId,
        answers: answers.value
      }),
    });
    if (resp.ok) {
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

export default function useTestCompletion() {
  return {
    testId,
    test,
    answers,
    submitting,
    successMessage,
    errorMessage,
    chartUrl,
    fetchTestAndInitAnswers,
    submitAnswers
  };
}
