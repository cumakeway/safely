@extends('layouts.master')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h4>Task Dashboard</h4>

    @if(auth()->user()->role->name === 'manager')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
            + Create Task
        </button>
    @endif
</div>


<form method="GET" class="row g-2 mb-4">
    <div class="col-md-3">
        <label class="fw-bold"> Status</label>
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="non_compliant" {{ request('status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
        </select>
    </div>

    <div class="col-md-3">
         <label class="fw-bold"> Assigned User</label>
        <select name="assigned_user_id" class="form-select">
            <option value="">All Users</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    {{ request('assigned_user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
         <label class="fw-bold"> Due </label>
        <select name="date_filter" class="form-select">
            <option value="">All</option>
            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="overdue" {{ request('date_filter') == 'overdue' ? 'selected' : '' }}>Overdue</option>
        </select>
    </div>

    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-dark w-50">Filter</button>
        <a href="/tasks" class="btn btn-outline-secondary w-50">Reset</a>
    </div>
</form>

<div id="alert-container"></div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>User</th>
            <th>Priority</th>
            <th>Due</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
        @php
            $dueDate = \Carbon\Carbon::parse($task->due_date)->toDateString();
            $today = now()->toDateString();
        @endphp
            <tr id="task-row-{{ $task->id }}"
            class="
                {{
                    $task->status != 'completed' && $dueDate < $today
                    ? 'table-danger'
                    : (
                        $task->status != 'completed' && $dueDate === $today
                        ? 'table-warning'
                        : ''
                    )
                }}
            ">

                <td>
                    <a href="{{ route('tasks.show', $task->id) }}">
                        {{ $task->title }}
                    </a>
                </td>
                <td>{{ $task->assignedUser->name }}</td>
                <td>
                    <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>

                <td>
                    @if($task->status == "pending")
                    <span class="badge bg-info status-text">
                        {{ $task->status }}
                    </span>
                    @elseif($task->status == "non_compliant")
                     <span class="badge bg-danger status-text">
                        {{ $task->status }}
                    </span>
                    @elseif($task->status == "completed")
                     <span class="badge bg-success status-text">
                        {{ $task->status }}
                    </span>
                    @endif
                </td>

                <td>
                    @if($task->status === 'pending')
                      @if(auth()->user()->isManager())
                        <button class="btn btn-warning btn-sm edit-task"
                                data-id="{{ $task->id }}">
                            Edit
                        </button>
                        @endif
                        <button class="btn btn-success btn-sm update-status"
                                data-id="{{ $task->id }}"
                                data-status="completed">
                             Completed
                        </button>

                       <button class="btn btn-danger btn-sm open-non-compliant-modal"
                                data-id="{{ $task->id }}">
                            Non-compliant
                        </button>
                        
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">
    {{ $tasks->links() }}
</div>

@include('tasks.partials.create-modal')

@include('tasks.partials.edit-modal')

<div class="modal fade" id="nonCompliantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Corrective Action Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="modal-task-id">

                <div class="mb-3">
                    <label>Corrective Action</label>
                    <textarea id="corrective-action-input" class="form-control" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" id="submit-non-compliant">
                    Submit
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')
<script>

    $('.open-non-compliant-modal').click(function () {
        let taskId = $(this).data('id');

        $('#modal-task-id').val(taskId);
        $('#corrective-action-input').val('');

        let modal = new bootstrap.Modal(document.getElementById('nonCompliantModal'));
        modal.show();
    });

    $('#submit-non-compliant').click(function () {

        let taskId = $('#modal-task-id').val();
        let correctiveAction = $('#corrective-action-input').val();

        if (!correctiveAction.trim()) {
            alert('Corrective action is required');
            return;
        }

        $.ajax({
            url: `/tasks/${taskId}/status`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: 'non_compliant',
                corrective_action: correctiveAction
            },
            success: function () {

                let row = $(`#task-row-${taskId}`);

                row.find('.status-text')
                    .text('non_compliant')
                    .removeClass('bg-info')
                    .addClass('bg-danger');

                row.find('td:last').html('');

                bootstrap.Modal.getInstance(document.getElementById('nonCompliantModal')).hide();
            }
        });
    });

    $('.update-status').click(function () {
        let taskId = $(this).data('id');

        $.ajax({
            url: `/tasks/${taskId}/status`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: 'completed'
            },
            success: function () {

                let row = $(`#task-row-${taskId}`);

                row.find('.status-text')
                    .text('completed')
                    .removeClass('bg-info')
                    .addClass('bg-success');

                row.find('td:last').html('');
            }
        });
    });

    $('#createTaskForm').submit(function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize();

        let submitBtn = $('#createTaskSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');
        let btnText = submitBtn.find('.btn-text');

        form.find('.invalid-feedback').text('');
        form.find('.form-control, .form-select').removeClass('is-invalid');

        spinner.removeClass('d-none');
        btnText.text('Creating...');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,

            success: function (response) {
            
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show">' +
                    response.message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );

                form[0].reset();
                bootstrap.Modal.getInstance(document.getElementById('createTaskModal')).hide();

                setTimeout(() => {
                    location.reload();
                }, 1500);

            },

            error: function (response) {
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    $.each(errors, function (key, value) {
                        let input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(value[0]);
                    });
                } else {
                    alert('Something went wrong');
                }
            },

            complete: function () {
                spinner.addClass('d-none');
                btnText.html('<i class="bi bi-plus-lg me-1"></i>Create Task');
            }
        });
    });

    $('.edit-task').click(function () {
        let taskId = $(this).data('id');
        
        $.ajax({
            url: `/tasks/${taskId}/edit`,
            method: 'GET',

            success: function (response) {

                let task = response.task;

                let formattedDate = task.due_date.split('T')[0];

                $('#edit-task-id').val(task.id);
                $('#editTaskForm input[name="title"]').val(task.title);
                $('#editTaskForm textarea[name="description"]').val(task.description);

                $('#editTaskForm input[name="due_date"]').val(formattedDate);

                $('#editTaskForm select[name="assigned_user_id"]').val(task.assigned_user_id);
                $('#editTaskForm select[name="priority"]').val(task.priority);

                let modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                modal.show();
            },

            error: function (response) {
                console.error(response.responseText);
                alert('Failed to load task data');
            }
        });

    });

    $('#editTaskForm').submit(function (e) {
        e.preventDefault();

        let taskId = $('#edit-task-id').val();
        let form = $(this);

        let formData = form.serialize();

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: `/tasks/${taskId}`,
            method: 'POST', 
            data: formData,

            success: function (response) {

                bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();

                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show">' +
                    response.message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );

                setTimeout(() => {
                    location.reload();
                }, 1500);
            },

            error: function (response) {

                if (response.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {
                        let input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(value[0]);
                    });
                }
            }
        });
    });

</script>
@endsection