<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
   public function index(Request $request)
   {
        $query = Task::with(['assignedUser', 'creator']);
        
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($userId = $request->input('assigned_user_id')) {
            $query->where('assigned_user_id', $userId);
        }
 
        $dateFilter = $request->input('date_filter', 'all');

        if ($dateFilter === 'today') {
            $query->whereDate('due_date', today());
        } elseif ($dateFilter === 'overdue') {
            $query->where('status', '!=', 'completed')
                  ->whereDate('due_date', '<', today());
        }

        $tasks = $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                       ->orderBy('due_date')
                       ->paginate(10)
                       ->withQueryString();

        $users = User::whereHas('role', fn ($q) => $q->where('name', 'worker'))
                     ->orderBy('name')
                     ->get();

        return view('tasks.index', compact('tasks', 'users', 'dateFilter'));
   }

   public function show(Task $task)
   {
        $task->load(['assignedUser', 'creator', 'activityLogs.user']);
        return view('tasks.show', compact('task'));
    }

   public function updateStatus(Request $request, Task $task)
   {
        $oldStatus = $task->status;

        $validated = $request->validate([
            'status' => 'required|in:completed,non_compliant',
            'corrective_action' => 'required_if:status,non_compliant|nullable|string',
        ]);

        $task->update([
            'status' => $validated['status'],
            'corrective_action' => $validated['corrective_action'] ?? null,
        ]);

        // Activity log
        $description = "Status changed from '{$oldStatus}' to '{$validated['status']}'.";
        if ($validated['status'] === 'non_compliant') {
            $description .= ' Corrective action recorded.';
        }

        ActivityLog::create([
            'task_id'     => $task->id,
            'user_id'     => $request->user()->id,
            'action'      => 'status_updated',
            'description' => $description,
            'changes'     => [
                'status' => ['from' => $oldStatus, 'to' => $validated['status']],
            ],
        ]);

        return response()->json(['success' => true]);
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $task = Task::create($data);

        ActivityLog::create([
            'task_id'     => $task->id,
            'user_id'     => $request->user()->id,
            'action'      => 'task_created',
            'description' => "Task '{$task->title}' was created.",
            'changes'     => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
        ]);
    }

    public function edit(Task $task)
    {
        return response()->json([
            'task' => $task
        ]);
    }

    public function update(StoreTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        $task->update($data);

        ActivityLog::create([
            'task_id'     => $task->id,
            'user_id'     => $request->user()->id,
            'action'      => 'task_updated',
            'description' => "Task '{$task->title}' was updated.",
            'changes'     => json_encode($data),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'task' => $task->load('assignedUser')
        ]);
    } 
}
