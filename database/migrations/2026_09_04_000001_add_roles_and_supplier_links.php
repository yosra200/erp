<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('admin')->after('password');
            $table->foreignId('supplier_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->decimal('commission_rate', 5, 2)->default(0)->after('supplier_id');
            $table->boolean('is_active')->default(true)->after('commission_rate');
        });

        Schema::table('suppliers', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->unique()->after('is_active')->constrained()->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('supplier_id')->nullable()->after('unit_id')->constrained()->nullOnDelete();
            $table->decimal('reorder_percent', 5, 2)->default(50)->after('min_stock');
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->foreignId('seller_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->decimal('commission_rate', 5, 2)->default(0)->after('paid');
            $table->decimal('commission_amount', 12, 2)->default(0)->after('commission_rate');
            $table->decimal('distribution_total', 12, 2)->default(0)->after('commission_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['seller_id', 'commission_rate', 'commission_amount', 'distribution_total']);
        });
        Schema::table('products', function (Blueprint $table): void {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'reorder_percent']);
        });
        Schema::table('suppliers', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['role', 'supplier_id', 'commission_rate', 'is_active']);
        });
    }
};
