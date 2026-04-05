<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alice   = DB::table('users')->where('email', 'alice@example.com')->value('id');
        $bob     = DB::table('users')->where('email', 'bob@example.com')->value('id');
        $charlie = DB::table('users')->where('email', 'charlie@example.com')->value('id');
        $diana   = DB::table('users')->where('email', 'diana@example.com')->value('id');
        $eve     = DB::table('users')->where('email', 'eve@example.com')->value('id');

        $tasks = [
             [
                'title'            => 'Inspect scaffold on Level 3',
                'description'      => 'Full safety inspection of scaffold structure on Level 3 east wing.',
                'due_date'         => now()->toDateString(),
                'assigned_user_id' => $charlie,
                'created_by'       => $alice,
                'priority'         => 'high',
                'status'           => 'pending',
                'corrective_action'=> null,
            ],
            [
                'title'            => 'Submit weekly progress report',
                'description'      => 'Compile and submit the weekly site progress report to head office.',
                'due_date'         => now()->subDays(2)->toDateString(), // overdue
                'assigned_user_id' => $diana,
                'created_by'       => $alice,
                'priority'         => 'medium',
                'status'           => 'pending',
                'corrective_action'=> null,
            ],
            [
                'title'            => 'Fire extinguisher check – Block A',
                'description'      => 'Check all fire extinguishers in Block A are within service date.',
                'due_date'         => now()->addDays(3)->toDateString(),
                'assigned_user_id' => $charlie,
                'created_by'       => $bob,
                'priority'         => 'high',
                'status'           => 'completed',
                'corrective_action'=> null,
            ],
            [
                'title'            => 'Clear debris from stairwell B',
                'description'      => 'Remove construction debris blocking stairwell B on floors 1–4.',
                'due_date'         => now()->subDays(1)->toDateString(), // overdue
                'assigned_user_id' => $eve,
                'created_by'       => $alice,
                'priority'         => 'high',
                'status'           => 'non_compliant',
                'corrective_action'=> 'Area cordoned off. Specialist waste contractor booked for tomorrow morning. Signage placed at all entry points.',
            ],
              [
                'title'            => 'PPE audit for new starters',
                'description'      => 'Verify all new starters have correct PPE and have signed equipment forms.',
                'due_date'         => now()->addDays(7)->toDateString(),
                'assigned_user_id' => $diana,
                'created_by'       => $bob,
                'priority'         => 'medium',
                'status'           => 'pending',
                'corrective_action'=> null,
            ],
            [
                'title'            => 'Electrical panel labelling',
                'description'      => 'Ensure all electrical panels on site are correctly labelled per regulation.',
                'due_date'         => now()->addDays(5)->toDateString(),
                'assigned_user_id' => $charlie,
                'created_by'       => $alice,
                'priority'         => 'low',
                'status'           => 'pending',
                'corrective_action'=> null,
            ],
              [
                'title'            => 'Update site induction records',
                'description'      => 'Ensure all induction records are up to date in the site register.',
                'due_date'         => now()->addDays(1)->toDateString(),
                'assigned_user_id' => $eve,
                'created_by'       => $bob,
                'priority'         => 'low',
                'status'           => 'completed',
                'corrective_action'=> null,
            ],
            [
                'title'            => 'Crane operator certification check',
                'description'      => 'Verify crane operators hold valid licences and certifications.',
                'due_date'         => now()->subDays(5)->toDateString(), // overdue
                'assigned_user_id' => $diana,
                'created_by'       => $alice,
                'priority'         => 'high',
                'status'           => 'non_compliant',
                'corrective_action'=> 'Two operators found with expired certs. Operations suspended. Renewal paperwork submitted. Expected resolution in 48 hours.',
            ],
        ];

        foreach ($tasks as $task) {
            DB::table('tasks')->insert(array_merge($task, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed some activity log entries for the completed/non-compliant tasks
        $completedTask    = DB::table('tasks')->where('title', 'Fire extinguisher check – Block A')->value('id');
        $nonCompliantTask = DB::table('tasks')->where('title', 'Clear debris from stairwell B')->value('id');


        DB::table('activity_logs')->insert([
            [
                'task_id'     => $completedTask,
                'user_id'     => $charlie,
                'action'      => 'status_updated',
                'description' => 'Status changed from pending to completed.',
                'changes'     => json_encode(['status' => ['from' => 'pending', 'to' => 'completed']]),
                'created_at'  => now()->subHours(2),
                'updated_at'  => now()->subHours(2),
            ],
            [
                'task_id'     => $nonCompliantTask,
                'user_id'     => $eve,
                'action'      => 'status_updated',
                'description' => 'Status changed from pending to non_compliant. Corrective action recorded.',
                'changes'     => json_encode(['status' => ['from' => 'pending', 'to' => 'non_compliant']]),
                'created_at'  => now()->subHour(),
                'updated_at'  => now()->subHour(),
            ],
        ]);
    }
}
