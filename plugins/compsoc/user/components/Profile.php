<?php namespace Compsoc\User\Components;

use Lang;
use Auth;
use Mail;
use Event;
use Flash;
use Input;
use Request;
use Redirect;
use Exception;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;

class Profile extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'compsoc.user::lang.profile.name',
            'description' => 'compsoc.user::lang.profile.desc'
        ];
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $this->page['user'] = $this->user();
    }

    /**
     * Returns the logged in user, if available, and touches
     * the last seen timestamp.
     * @return RainLab\User\Models\User
     */
    public function user()
    {
        if (!$user = Auth::getUser()) {
            return null;
        }

        $user->touchLastSeen();

        return $user;
    }
}