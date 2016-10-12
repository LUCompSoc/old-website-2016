<?php namespace CompSoc\Jobs\Controllers;

use Flash;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use ApplicationException;
use CompSoc\Jobs\Models\Job;

/**
 * Job Listings Back-end Controller
 */
class Listings extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['compsoc.jobs.access_other_posts', 'rainlab.jobs.access_posts'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CompSoc.Jobs', 'jobs', 'listings');
    }

    public function index()
    {
        $this->vars['listingsTotal'] = Job::count();
        $this->vars['listingsPublished'] = Job::isPublished()->count();
        $this->vars['listingsDrafts'] = $this->vars['listingsTotal'] - $this->vars['listingsPublished'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('new_listing');

    //  $this->addCss('/plugins/compsoc/jobs/assets/css/compsoc.listing-preview.css');
    //  $this->addJs('/plugins/compsoc/jobs/assets/js/post-form.js');

        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
    //  $this->addCss('/plugins/rainlab/blog/assets/css/rainlab.blog-preview.css');
    //  $this->addJs('/plugins/rainlab/blog/assets/js/post-form.js');

        return $this->asExtension('FormController')->update($recordId);
    }

    public function listExtendQuery($query)
    {
        if (!$this->user->hasAnyAccess(['compsoc.jobs.access_other_posts'])) {
            $query->where('user_id', $this->user->id);
        }
    }

    public function formExtendQuery($query)
    {
        if (!$this->user->hasAnyAccess(['compsoc.jobs.access_other_posts'])) {
            $query->where('user_id', $this->user->id);
        }
    }

    public function formExtendFieldsBefore($widget)
    {
        if (!$model = $widget->model) {
            return;
        }

        if ($model instanceof Job && $model->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')) {
            $widget->secondaryTabs['fields']['content']['type'] = 'RainLab\Blog\FormWidgets\MLBlogMarkdown';
        }
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $jobId) {
                if ((!$job = Job::find($jobId)) || !$job->canEdit($this->user))
                    continue;

                $job->delete();
            }

            Flash::success('Successfully deleted those posts.');
        }

        return $this->listRefresh();
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        if (!$record->published)
            return 'safe disabled';
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

    public function onRefreshPreview()
    {
        $data = post('Job');

        $previewHtml = Job::formatHtml($data['content'], true);

        return [
            'preview' => $previewHtml
        ];
    }

}