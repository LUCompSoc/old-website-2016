<?php namespace Radiantweb\Proevents;

use Backend;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use Backend\Models\User as BackendUserModel;
use Radiantweb\Proevents\Models\Calendar;
use Event;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'radiantweb.proevents::lang.plugin.name',
            'description' => 'radiantweb.proevents::lang.plugin.description',
            'author'      => '://radiantweb',
            'icon'        => 'icon-calendar-o'
        ];
    }

    public function boot()
    {
        Event::listen('pages.menuitem.listTypes', function() {
                return [
                    'proevents-calendar'=>'radiantweb.proevents::lang.menuitems.calendar',
                    'all-proevents-calendars'=>'radiantweb.proevents::lang.menuitems.allcalendars',
                ];
            });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
                if ($type == 'proevents-calendar' || $type == 'all-proevents-calendars')
                    return Calendar::getMenuTypeInfo($type);
            });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
                if ($type == 'proevents-calendar' || $type == 'all-proevents-calendars')
                    return Calendar::resolveMenuItem($item, $url, $theme);
            });
    }

    public function registerComponents()
    {
        return [
            'Radiantweb\Proevents\Components\Event' => 'proEvent',
            'Radiantweb\Proevents\Components\EventList' => 'proEventList',
            'Radiantweb\Proevents\Components\EventCalendar' => 'proEventCalendar',
        ];
    }

    public function registerNavigation()
    {
        return [
            'proevents' => [
                'label'       => 'radiantweb.proevents::lang.plugin.name',
                'url'         => Backend::url('radiantweb/proevents/events'),
                'icon'        => 'icon-calendar',
                'permissions' => ['radiantweb.proevents.*'],
                'order'       => 500,

                'sideMenu' => [
                    'events' => [
                        'label'       => 'radiantweb.proevents::lang.sidemenu.events',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('radiantweb/proevents/events'),
                        'permissions' => ['radiantweb.proevents.access_events'],
                    ],
                    'generated_dates' => [
                        'label'       => 'radiantweb.proevents::lang.sidemenu.generated',
                        'icon'        => 'icon-list-alt',
                        'url'         => Backend::url('radiantweb/proevents/generated_dates'),
                        'permissions' => ['radiantweb.proevents.access_events'],
                    ],
                    'calendars' => [
                        'label'       => 'radiantweb.proevents::lang.sidemenu.calendars',
                        'icon'        => 'icon-calendar-o',
                        'url'         => Backend::url('radiantweb/proevents/calendars'),
                        'permissions' => ['radiantweb.proevents.access_calendars'],
                    ],
                ]

            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'radiantweb.proevents.access_calendars' => ['label' => 'radiantweb.proevents::lang.permissions.mangecalendars', 'tab' => 'ProEvents'],
            'radiantweb.proevents.access_events' => ['label' => 'radiantweb.proevents::lang.permissions.mangeevents', 'tab' => 'ProEvents']
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'ProEvents',
                'description' => 'radiantweb.proevents::lang.settings.description',
                'icon'        => 'icon-calendar',
                'class'       => 'Radiantweb\Proevents\Models\Settings',
                'order'       => 100
            ]
        ];
    }

    public function registerFormWidgets()
	{
	    return [
	        'Radiantweb\Proevents\Modules\Backend\Formwidgets\Multidate' => [
	            'label' => 'MultiDate',
	            'code' => 'multidate'
	        ],
            'Radiantweb\Proevents\Modules\Backend\Formwidgets\Time' => [
                'label' => 'Time',
                'code' => 'time'
            ],
            'Radiantweb\Proevents\Modules\Backend\Formwidgets\Rcolorpicker' => [
                'label' => 'Colorpicker',
                'code' => 'rcolorpicker'
            ]
	    ];
	}

    public function registerMarkupTags()
    {
        // Check the translate plugin is installed
        $filters = array();
        if (!PluginManager::instance()->exists('RainLab.Translate')) {
            $filters['_'] = ['Lang', 'get'];
            $filters['__'] = ['Lang', 'choice'];
        }

        return [
            'functions' => [
                'getAuthorInfo' => [$this, 'getAuthorInfo'],
            ],
            'filters' => $filters
        ];
    }

    public function getAuthorInfo($id)
    {
        $user = BackendUserModel::where('id',$id)->first();
        if($user->avatar){
            $user->image = $user->avatar->getThumb(100, 100, ['mode' => 'crop']);
        }
        return $user;
    }

}
