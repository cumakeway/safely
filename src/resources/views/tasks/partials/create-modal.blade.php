<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createTaskForm" action="{{ route('task.create') }}" method="POST"  class="modal-content">  
            @csrf

            <div class="modal-header">
                <h5>Create Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-2">
                    <label>Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control"></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Assign User <span class="text-danger">*</span></label>
                    <select name="assigned_user_id" class="form-select">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

            </div>

            <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-update-status" id="createTaskSubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <span class="btn-text"><i class="bi bi-plus-lg me-1"></i>Create Task</span>
                    </button>
            </div>

        </form>
    </div>
</div>