<?php namespace Compsoc\User;

use App;
use Backend;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Illuminate\Foundation\AliasLoader;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;

/**
 * integration Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'CompSoc Profile',
            'description' => 'Adds integration features for use with Lancaster University.',
            'author'      => 'Alexander Jung',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $alias = AliasLoader::getInstance();

        $alias->alias('JWT', 'Compsoc\User\Facades\JWT');
        App::singleton('JWT', function() {
            return \Compsoc\User\Classes\JWT::instance();
        });

        $alias->alias('Mattermost', 'Compsoc\User\Facades\Mattermost');
        App::singleton('Mattermost', function() {
            return \Compsoc\User\Classes\Mattermost::instance();
        });

        $alias->alias('Auth', 'RainLab\User\Facades\Auth');
        App::singleton('user.auth', function() {
            return \RainLab\User\Classes\AuthManager::instance();
        });
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

        UserModel::extend(function($model) {
            $model->hasOne['profile'] = ['Compsoc\User\Models\Profile'];
        });

        UsersController::extendListColumns(function($list, $model)
        {
            if(!$model instanceof UserModel)
                return;

            $list->addColumns([
                'university_id' => [
                    'label' =>  'compsoc.user::lang.plugin.profile.fields.university_id'
                ],
                'union_id' => [
                    'label' =>  'compsoc.user::lang.plugin.profile.fields.union_id'
                ]
            ]);

        });

        UsersController::extendFormFields(function($form, $model, $context)
        {
            if(!$model instanceof UserModel)
                return;

            $form->addTabFields([
                'university_id' => [
                    'label' => 'compsoc.user::lang.plugin.profile.fields.university_id',
                    'tab' => 'compsoc.user::lang.plugin.profile.tab_label'
                ],
                'union_id' => [
                    'label' => 'compsoc.user::lang.plugin.profile.fields.union_id',
                    'tab' => 'compsoc.user::lang.plugin.profile.tab_label'
                ],
                'irc_id' => [
                    'label' => 'compsoc.user::lang.plugin.profile.fields.irc_id',
                    'tab' => 'compsoc.user::lang.plugin.profile.tab_label'
                ],
                'title' => [
                    'label' => 'compsoc.user::lang.plugin.profile.fields.title',
                    'tab' => 'compsoc.user::lang.plugin.profile.tab_label'
                ],
                'subscribed_newsletters' => [
                    'label' => 'compsoc.user::lang.plugin.profile.fields.subscribed_newsletters',
                    'tab' => 'compsoc.user::lang.plugin.profile.tab_label'
                ],
            ]);
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Compsoc\User\Components\LoginForm' => 'loginForm',
            'Compsoc\User\Components\RegisterForm' => 'registerForm',
            'Compsoc\User\Components\Profile' => 'profile',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'compsoc.profile.some_permission' => [
                'tab' => 'profile',
                'label' => 'Some permission'
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
        return []; // Remove this line to activate

        return [
            'profile' => [
                'label'       => 'profile',
                'url'         => Backend::url('compsoc/profile/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['compsoc.profile.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'compsoc.user::lang.settings.menu_label',
                'description' => 'compsoc.user::lang.settings.menu_description',
                'category'    => SettingsManager::CATEGORY_USERS,
                'icon'        => 'icon-cog',
                'class'       => 'Compsoc\User\Models\Settings',
                'order'       => 500,
                'permissions' => ['rainlab.users.access_settings'],
            ]
        ];
    }

}
