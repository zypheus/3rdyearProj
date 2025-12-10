<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->date('due_date');
            $table->decimal('amount', 12, 2);
            $table->decimal('principal_component', 12, 2)->default(0);
            $table->decimal('interest_component', 12, 2)->default(0);
            $table->enum('status', ['planned','confirmed','paid','skipped'])->default('planned');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->unique(['loan_id','sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
