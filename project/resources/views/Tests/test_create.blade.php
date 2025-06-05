{{-- filepath: c:\xampp\htdocs\PsyForm\project\resources\views\test_create.blade.php --}}
@extends('layouts.app')

@section('content')
  <test-create-page :questions='@json($questions)'/>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const { createApp, ref } = Vue;
    createApp({
        setup() {
            const questions = ref(window.testCreateData || []);
            const testName = ref('');
            const selectedExistingQuestionId = ref('');
            const newQuestionText = ref('');
            const newQuestionType = ref('text');
            const testQuestions = ref([]);
            const success = ref('');
            const error = ref('');

            function addExistingQuestion() {
                const q = questions.value.find(q => q.id == selectedExistingQuestionId.value);
                if (q && !testQuestions.value.some(tq => tq.id === q.id)) {
                    testQuestions.value.push({ id: q.id, text: q.text, type: q.type || 'text' });
                }
                selectedExistingQuestionId.value = '';
            }

            function addNewQuestion() {
                if (!newQuestionText.value.trim()) return;
                testQuestions.value.push({ id: null, text: newQuestionText.value, type: newQuestionType.value });
                newQuestionText.value = '';
                newQuestionType.value = 'text';
            }

            function removeQuestion(idx) {
                testQuestions.value.splice(idx, 1);
            }

            async function submitTest() {
                error.value = '';
                success.value = '';
                if (!testName.value.trim() || testQuestions.value.length === 0) {
                    error.value = 'Test name and at least one question are required.';
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
                            name: testName.value,
                            questions: testQuestions.value,
                        }),
                    });
                    if (!response.ok) {
                        const data = await response.json();
                        error.value = data.message || 'Failed to create test.';
                    } else {
                        success.value = 'Test created successfully!';
                        testName.value = '';
                        testQuestions.value = [];
                    }
                } catch (e) {
                    error.value = 'An error occurred. Please try again.';
                }
            }

            return {
                questions,
                testName,
                selectedExistingQuestionId,
                newQuestionText,
                newQuestionType,
                testQuestions,
                addExistingQuestion,
                addNewQuestion,
                removeQuestion,
                submitTest,
                success,
                error,
            };
        }
    }).mount('#test-create-app');
});
</script>
@endpush