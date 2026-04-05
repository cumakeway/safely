@extends('layouts.master')

@section('content')

<h4 class="mb-3">Task Details</h4>

<div class="card mb-3">
    <div class="card-body">

        <h5>{{ $task->title }}</h5>
        <p>{{ $task->description }}</p>

        <hr>

        <p><strong>Assigned To:</strong> {{ $task->assignedUser->name }}</p>
        <p><strong>Created By:</strong> {{ $task->creator->name }}</p>
        <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
        <p><strong>Status:</strong> {{ $task->status }}</p>
        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</p>

        @if($task->corrective_action)
            <div class="alert alert-danger mt-3">
                <strong>Corrective Action:</strong><br>
                {{ $task->corrective_action }}
            </div>
        @endif

    </div>
</div>

@if($task->activityLogs->count())
    <div class="card">
        <div class="card-body">
            <h5>Activity Log</h5>

            <ul class="list-group">
                @foreach($task->activityLogs as $log)
                    <li class="list-group-item">
                        <strong>{{ $log->user->name }}</strong>:
                        {{ $log->description }}
                        <br>
                        <small class="text-muted">{{ $log->created_at }}</small>
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
@endif

<a href="/tasks" class="btn btn-secondary mt-3">← Back</a>

@endsection