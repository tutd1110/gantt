<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gantttasks', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->integer('task_id');
            $table->text('name')->nullable();
            $table->integer('progress')->nullable();
            $table->text('description')->nullable();
            $table->integer('level');
            $table->string('status_alias');
            $table->string('status_title');
            $table->string('depends')->nullable();
            $table->boolean('canWrite');
            $table->bigInteger('start');
            $table->bigInteger('end');
            $table->integer('duration')->nullable();
            $table->boolean('startIsMilestone');
            $table->boolean('endIsMilestone');
            $table->boolean('collapsed');
            $table->integer('user_id')->nullable();
            $table->boolean('hasChild');
            $table->integer('position')->nullable()->comment('Số thứ tự task');
            $table->timestamps();

            $table->index(['id','start','end','user_id','position','project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gantttasks');
    }
};
