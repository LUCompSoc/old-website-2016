<?php namespace CompSoc\Jobs\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('compsoc_job_listings', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('company');
            $table->string('company_url')->nullable();
            $table->string('industry')->nullable();
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->string('earnings')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('pinned')->default(false);
            $table->string('reference')->nullable();
            $table->string('contact_email')->nullable();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('compsoc_job_listings');
    }
}
