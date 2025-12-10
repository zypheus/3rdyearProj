<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payment_schedule_id')->nullable()->after('payment_method')->constrained('payment_schedules')->nullOnDelete();
            $table->boolean('is_advance')->default(false)->after('payment_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_schedule_id']);
            $table->dropColumn(['payment_schedule_id','is_advance']);
        });
    }
};
