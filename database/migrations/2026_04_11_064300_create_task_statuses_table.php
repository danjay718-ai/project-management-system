<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create the task_statuses lookup table
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();       // e.g. "not_started"
            $table->string('label');                  // e.g. "Not Started"
            $table->string('color')->default('slate'); // Tailwind color key
            $table->integer('position')->default(0);  // Sort order for kanban
            $table->timestamps();
        });

        // 2. Add foreign key to tasks table, then migrate data, then drop old column
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('task_status_id')
                  ->nullable()
                  ->after('description')
                  ->constrained('task_statuses')
                  ->cascadeOnDelete();
        });

        // 3. Migrate existing string status values to the new FK
        //    (Only works if task_statuses have been seeded first — 
        //     for fresh installs, use migrate:fresh --seed)
        $statusMap = \App\Models\TaskStatus::pluck('id', 'name');
        if ($statusMap->isNotEmpty()) {
            foreach ($statusMap as $name => $id) {
                \DB::table('tasks')->where('status', $name)->update(['task_status_id' => $id]);
            }
            // Assign any unmatched tasks to the first status
            $defaultId = $statusMap->first();
            \DB::table('tasks')->whereNull('task_status_id')->update(['task_status_id' => $defaultId]);
        }

        // 4. Drop old status string column and make FK non-nullable
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the status string column
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status')->default('not_started')->after('description');
            $table->index('status');
        });

        // Migrate data back from FK to string
        $statusMap = \App\Models\TaskStatus::pluck('name', 'id');
        foreach ($statusMap as $id => $name) {
            \DB::table('tasks')->where('task_status_id', $id)->update(['status' => $name]);
        }

        // Drop the FK column
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_status_id');
        });

        Schema::dropIfExists('task_statuses');
    }
};
