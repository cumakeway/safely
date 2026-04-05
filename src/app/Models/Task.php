<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

     protected $fillable = [
        'title',
        'description',
        'due_date',
        'assigned_user_id',
        'created_by',
        'priority',
        'status',
        'corrective_action',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
 
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }
}
