<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionProjecttool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_projecttool', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->nullable()->constrained();
            $table->foreignId('project_tools_id')->constrained();
            $table->unique(['inspection_id','project_tools_id']);
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
        Schema::dropIfExists('inspection_projecttool');
    }
}
