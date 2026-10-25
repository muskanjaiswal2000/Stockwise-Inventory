<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        $roleTable = config('permission.table_names.roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles');
        $rolePivotKey = config('permission.column_names.role_pivot_key') ?: 'role_id';
        $modelKey = config('permission.column_names.model_morph_key') ?: 'model_id';

        foreach (['admin', 'staff'] as $roleName) {
            DB::table($roleTable)->insertOrIgnore([
                'name' => $roleName,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleIds = DB::table($roleTable)
            ->where('guard_name', 'web')
            ->whereIn('name', ['admin', 'staff'])
            ->pluck('id', 'name');

        foreach (DB::table('users')->select('id', 'role')->orderBy('id')->get() as $user) {
            $roleName = $user->role === 'admin' ? 'admin' : 'staff';
            $roleId = $roleIds[$roleName];

            $assignment = [
                $rolePivotKey => $roleId,
                'model_type' => User::class,
                $modelKey => $user->id,
            ];

            if (! DB::table($modelHasRolesTable)->where($assignment)->exists()) {
                DB::table($modelHasRolesTable)->insert($assignment);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff');
        });

        $roleTable = config('permission.table_names.roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles');
        $rolePivotKey = config('permission.column_names.role_pivot_key') ?: 'role_id';
        $modelKey = config('permission.column_names.model_morph_key') ?: 'model_id';

        foreach (DB::table('users')->select('id')->orderBy('id')->get() as $user) {
            $roleName = DB::table($modelHasRolesTable)
                ->join($roleTable, "{$roleTable}.id", '=', "{$modelHasRolesTable}.{$rolePivotKey}")
                ->where("{$modelHasRolesTable}.model_type", User::class)
                ->where("{$modelHasRolesTable}.{$modelKey}", $user->id)
                ->whereIn("{$roleTable}.name", ['admin', 'staff'])
                ->where("{$roleTable}.guard_name", 'web')
                ->value("{$roleTable}.name");

            if ($roleName !== null) {
                DB::table('users')->where('id', $user->id)->update(['role' => $roleName]);
            }
        }
    }
};
