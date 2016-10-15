<?php namespace CompSoc\Jobs;

use Backend;
use System\Classes\PluginBase;

/**
 * jobs Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'compsoc.jobs::lang.plugin.name',
            'description' => 'compsoc.jobs::lang.plugin.description',
            'author'      => 'Alexander Jung',
            'icon'        => 'icon-copy'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'CompSoc\Jobs\Components\JobListing'       => 'jobListing',
            'CompSoc\Jobs\Components\JobListings'      => 'jobListings'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'compsoc.jobs.access_posts' => [
                'tab'   => 'compsoc.jobs::lang.jobs.tab',
                'label' => 'compsoc.jobs::lang.jobs.access_posts'
            ],
            'compsoc.jobs.access_publish' => [
                'tab'   => 'compsoc.jobs::lang.jobs.tab',
                'label' => 'compsoc.jobs::lang.jobs.access_publish'
            ],
            'compsoc.jobs.access_other_posts' => [
                'tab'   => 'compsoc.jobs::lang.jobs.tab',
                'label' => 'compsoc.jobs::lang.jobs.access_other_posts'
            ],
            'compsoc.jobs.access_import_export' => [
                'tab'   => 'compsoc.jobs::lang.jobs.tab',
                'label' => 'compsoc.jobs::lang.jobs.access_import_export'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [

            'jobs' => [
                'label'       => 'compsoc.jobs::lang.jobs.menu_label',
                'url'         => Backend::url('compsoc/jobs/listings'),
                'icon'        => 'icon-copy',
            //  'iconSvg'     => 'plugins/compsoc/jobs/assets/images/jobs-icon.svg',
                'permissions' => ['compsoc.jobs.*'],
                'order'       => 30,

                'sideMenu' => [
                    'new_listing' => [
                        'label'       => 'compsoc.jobs::lang.jobs.new_listing',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('compsoc/jobs/listings/create'),
                        'permissions' => ['compsoc.jobs.access_posts']
                    ],
                    'listings' => [
                        'label'       => 'compsoc.jobs::lang.jobs.listings',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('compsoc/jobs/listings'),
                        'permissions' => ['compsoc.jobs.access_posts']
                    ]
                ]
            ]
        ];
    }
}
