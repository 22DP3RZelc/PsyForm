@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl mt-10">
    <h1 class="text-3xl font-bold mb-6 text-teal-700 dark:text-teal-300">All Tests</h1>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
        @if($tests->isEmpty())
            <p class="text-gray-600 dark:text-gray-300">No tests found.</p>
        @else
            <ul>
                @foreach($tests as $test)
                    <li class="mb-4 border-b pb-2">
                        <span class="font-semibold">{{ $test->name }}</span>
                        {{-- Add more test info/actions here if needed --}}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
