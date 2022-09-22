<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id') -> constrained();
            $table->foreignId('project_id') -> constrained();
            $table->foreignId('user_id') -> constrained();
            //$table->primary(['tool_id', 'project_id']);
            $table->softDeletes();
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
        Schema::dropIfExists('project_tools');
    }
}
