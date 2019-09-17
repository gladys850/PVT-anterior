<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class NullUnusedColsOnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->unique('username');
            $table->boolean('active')->default(true);
            $table->boolean('is_commission')->nullable(true)->default(null)->change();
            $table->unsignedInteger('city_id')->nullable(true)->default(null)->change();
        });
        $this->change_status(true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropUnique(['username']);
            $table->dropColumn(['active']);
            $table->boolean('is_commission')->nullable(false)->change();
            $table->unsignedInteger('city_id')->nullable(false)->change();
        });
        $this->change_status(false);
    }

    private function change_status($nullable)
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('status', 'temp_status');
        });
        Schema::table('users', function (Blueprint $table) use ($nullable) {
            if ($nullable) {
                $table->enum('status', ['active', 'inactive'])->nullable($nullable);
            } else {
                $table->enum('status', ['active', 'inactive'])->nullable($nullable)->default('active');
            }
        });
        $all = json_decode(DB::table('users')->get(), true);
        foreach ($all as $item)
        {
            $fill = [
                'status' => $item['temp_status']
            ];
            if ($nullable) {
                $fill['active'] = ($item['temp_status'] == 'active') ? true : false;
            }
            DB::table('users')->where('id', $item['id'])->update($fill);
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('temp_status');
        });
    }
}
