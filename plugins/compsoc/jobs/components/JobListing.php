<?php namespace CompSoc\Jobs\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use CompSoc\Jobs\Models\Job;

class JobListing extends ComponentBase
{
    /**
     * @var CompSoc\Jobs\Models\Job The job model used for display.
     */
    public $job;

    public function componentDetails()
    {
        return [
            'name'        => 'compsoc.jobs::lang.settings.job_title',
            'description' => 'compsoc.jobs::lang.settings.job_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title'       => 'compsoc.jobs::lang.settings.job_id',
                'description' => 'compsoc.jobs::lang.settings.job_id_description',
                'default'     => '{{ :id }}',
                'type'        => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->job = $this->page['job'] = $this->loadJob();
    }

    protected function loadJob()
    {
        $id = $this->property('id');

        $job = new Job;

        $job = $job->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $job->transWhere('id', $id)
            : $job->where('id', $id);

        $job = $job->isPublished()->first();
        $job->expired = strtotime($job->deadline) < time();

        return $job;
    }
}
