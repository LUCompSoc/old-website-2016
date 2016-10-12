<?php namespace CompSoc\Jobs\Components;

use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use CompSoc\Jobs\Models\Job;

class JobListings extends ComponentBase
{
    /**
     * A collection of jobs to display
     * @var Collection
     */
    public $jobs;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noJobsMessage;

    /**
     * Reference to the page name for linking to jobs.
     * @var string
     */
    public $jobPage;

    /**
     * If the job list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'compsoc.jobs::lang.component.jobs_title',
            'description' => 'compsoc.jobs::lang.component.jobs_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'compsoc.jobs::lang.component.jobs_pagination',
                'description' => 'compsoc.jobs::lang.component.jobs_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'compsoc.jobs::lang.component.jobs_filter',
                'description' => 'compsoc.jobs::lang.component.jobs_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'jobsPerPage' => [
                'title'             => 'compsoc.jobs::lang.component.jobs_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'compsoc.jobs::lang.component.jobs_per_page_validation',
                'default'           => '10',
            ],
            'noJobsMessage' => [
                'title'        => 'compsoc.jobs::lang.component.jobs_no_jobs',
                'description'  => 'compsoc.jobs::lang.component.jobs_no_jobs_description',
                'type'         => 'string',
                'default'      => 'No jobs found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'compsoc.jobs::lang.component.jobs_order',
                'description' => 'compsoc.jobs::lang.component.jobs_order_description',
                'type'        => 'dropdown',
                'default'     => ['pinned desc', 'published_at desc']
            ],
            'jobPage' => [
                'title'       => 'compsoc.jobs::lang.component.jobs_page',
                'description' => 'compsoc.jobs::lang.component.jobs_page_description',
                'type'        => 'dropdown',
                'default'     => 'job',
                'group'       => 'Links',
            ],
        ];
    }

    public function getJobPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions()
    {
        return Job::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->jobs = $this->page['jobs'] = $this->listJobs();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->jobs->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noJobsMessage = $this->page['noJobsMessage'] = $this->property('noJobsMessage');

        /*
         * Page links
         */
        $this->jobPage = $this->page['jobPage'] = $this->property('jobPage');
    }

    protected function listJobs()
    {
        /*
         * List all the jobs, eager load their categories
         */
        $jobs = Job::listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('jobsPerPage'),
            'search'     => trim(input('search'))
        ]);

        /*
         * Add a "url" helper attribute for linking to each job 
         */
        $jobs->each(function($job) {
            $job->setUrl($this->jobPage, $this->controller);
        });

        return $jobs;
    }
}
