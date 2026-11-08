
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')
                ->default(false);

            $table->unsignedTinyInteger('admin_level')
                ->default(0);

            $table->json('admin_permissions')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_super_admin',
                'admin_level',
                'admin_permissions',
                'is_active',
            ]);
        });
    }
};
