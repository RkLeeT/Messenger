<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\Auth;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('groups', function (Blueprint $table) {
            static $number = 1;
            $table->increments('id');
            $table->string('name')->default('Group'.$number++);
            $table->string('admin')->default(Auth::id());
            $table->integer('count')->default(1);
            $table->string('profile_image')->default("http://via.placeholder.com/150x150");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
