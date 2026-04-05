<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editTaskForm" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5>Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit-task-id">

                <div class="mb-2">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="mb-2">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Assign User</label>
                    <select name="assigned_user_id" class="form-select">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-2">
                    <label>Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    Update Task
                </button>
            </div>

        </form>
    </div>
</div>