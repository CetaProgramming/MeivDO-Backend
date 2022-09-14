<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_tools', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('image')->nullable();
            $table->foreignId('category_tools_id')->constrained();
            $table->string('description');
            $table->boolean('active');
            $table->foreignId('user_id') -> constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_tools');
    }
}
