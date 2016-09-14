<?php namespace Radiantweb\Proevents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCalendarsTable extends Migration
{

    public function up()
    {
        Schema::create('radiantweb_proevents_calendars', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->index();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('color')->nullable();
            $table->timestamps();
        });

        Schema::create('radiantweb_events_calendars', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('event_id')->unsigned();
            $table->integer('calendar_id')->unsigned();
            $table->primary(['event_id', 'calendar_id']);
        });
    }

    public function down()
    {
        Schema::drop('radiantweb_proevents_calendars');
        Schema::drop('radiantweb_events_calendars');
    }

}
