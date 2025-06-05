@extends('layouts.app')

@section('content')
    <test-complete-page :test-id="{{ $testId }}"/>
    <div id="test-complete-app" data-test-id="{{ $testId }}"></div>
    <script>
        window.testId = {{ $testId }};
    </script>
    <script type="module">
    import { createApp } from 'vue';
    import TestCompletePage from '../../js/components/TestCompletePage.vue';
    const testId = document.getElementById('test-complete-app').dataset.testId;
    createApp(TestCompletePage, { testId }).mount('#test-complete-app');
    </script>
@endsection
