<?php namespace Compsoc\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddUserProfileFields extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->integer('university_id')->nullable();
            $table->integer('union_id')->nullable();
            $table->integer('irc_id')->nullable();
            $table->string('title')->nullable();
            $table->string('position')->nullable();
            $table->string('subscribed_newsletters')->default('[]');
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn(['university_id', 'union_id', 'irc_id', 'title', 'subscribed_newsletters', 'position']);
        });
    }
}