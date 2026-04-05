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

{{-- TASK TABLE --}}
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
            <tr id="task-row-{{ $task->id }}"
                class="{{ $task->due_date < today() && $task->status != 'completed' ? 'table-danger' : '' }}">

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
                <td>{{ $task->due_date }}</td>

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
                        @if(auth()->user()->role->name === 'manager')
                        <button class="btn btn-warning btn-sm update-status"
                                data-id="{{ $task->id }}"
                                data-status="non_compliant">
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

@include('tasks.partials.create-modal')

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

// OPEN MODAL
$('.open-non-compliant-modal').click(function () {
    let taskId = $(this).data('id');

    $('#modal-task-id').val(taskId);
    $('#corrective-action-input').val('');

    let modal = new bootstrap.Modal(document.getElementById('nonCompliantModal'));
    modal.show();
});

// SUBMIT NON-COMPLIANT
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

// COMPLETED (unchanged)
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

// $('#createTaskForm').on('submit', function (e) {
//         e.preventDefault();
//         const $form = $(this);
//         const $btn  = $('#createTaskSubmitBtn').addClass('loading').prop('disabled', true);
 
//         $.ajax({
//             url:  $form.attr('action'),
//             method:  'POST',
//             headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
//             data:    $form.serialize(),
//             success(data) {
//               $('#createTaskModal').hide();
//                 flashAlert('success', '<i class="bi bi-check-circle me-2"></i>Task created! Refreshing…');
//                 setTimeout(() => location.reload(), 1200);
//             },
//             error(xhr) {
              
//                 $form.find('.is-invalid').removeClass('is-invalid');
//                 $form.find('.invalid-feedback').text('');
 
//                 if (xhr.status === 422) {
//                     const errors = xhr.responseJSON?.errors ?? {};
//                     $.each(errors, function (field, messages) {
//                         const $input = $form.find('[name="' + field + '"]');
//                         $input.addClass('is-invalid');
//                         $input.siblings('.invalid-feedback').text(messages[0]);
//                     });
//                 } else {
//                     flashAlert('danger', 'Something went wrong. Please try again.');
//                 }
//             },
//             complete() {
//                 $btn.removeClass('loading').prop('disabled', false);
//             }
//         });
// });

$('#createTaskForm').submit(function (e) {
    e.preventDefault();

    let form = $(this);
    let url = form.attr('action');
    let formData = form.serialize();

    let submitBtn = $('#createTaskSubmitBtn');
    let spinner = submitBtn.find('.spinner-border');
    let btnText = submitBtn.find('.btn-text');

    // Reset errors
    form.find('.invalid-feedback').text('');
    form.find('.form-control, .form-select').removeClass('is-invalid');

    // Show loading
    spinner.removeClass('d-none');
    btnText.text('Creating...');

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,

        success: function (response) {
            $('<div class="alert alert-success alert-dismissible fade show mt-2">' +
                response.message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>')
            .prependTo('.container');
            
            form[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('createTaskModal')).hide();
            location.reload(); 
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
</script>
@endsection