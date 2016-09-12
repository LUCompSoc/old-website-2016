<?php namespace Radiantweb\Proevents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddLocationInfo extends Migration
{

    public function up()
    {
        Schema::table('radiantweb_events', function($table)
        {
            $table->text('location_name')->nullable();
            $table->text('location_address')->nullable();
            $table->text('contact_email')->nullable();
        });
    }
}