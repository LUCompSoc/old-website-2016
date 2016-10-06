<?php namespace Compsoc\User\Components;

use Auth;
use Lang;
use Input;
use Flash;
use Session;
use Redirect;
use Exception;
use Validator;
use ValidationException;
use ApplicationException;
use Compsoc\User\Classes\JWT;
use Cms\Classes\ComponentBase;
use Compsoc\User\Models\Settings;
use RainLab\User\Models\User as UserModel;

class LoginForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'compsoc.user::lang.login.name',
            'description' => 'compsoc.user::lang.login.desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'compsoc.user::lang.login.redirect_to',
                'description' => 'compsoc.user::lang.login.redirect_to_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ]
        ];
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        try
        {
            Flash::purge();

            $then = Input::get('then');

            if($then == 'register')
                Session::set('request_register', true);

            $settings = Settings::instance();
            $private_key = $settings->get('jwt_private_key');
            $jwt_request = Input::get('jwt');

            $this->page['jwt_auth_url'] = $settings->get('jwt_auth_url');

            if(is_null($jwt_request))
                return Redirect::to($this->page['jwt_auth_url']);

            $this->page['authorised'] = true;
            $payload = JWT::decode($jwt_request, $private_key, array("HS256"));

            if(is_null($payload))
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.login.error.bad_response'));
            }
            else if(Session::get('request_register'))
            {
                Session::set('register_user', $payload);
                Session::set('request_register', null);
                return Redirect::to('/register');
            }
            else if(!$user = UserModel::findByEmail($payload->mail))
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.login.error.not_registerd'));
            } 
            else
            {
                $user = Auth::findUserByCredentials(['username' => $payload->username, 'email' => $payload->mail]);
                Auth::login($user, true);

                /*
                 * Redirect to the intended page after successful sign in
                 */
                $redirectUrl = $this->pageUrl($this->property('redirect'))
                    ?: $this->property('redirect');

                if ($redirectUrl = post('redirect', $redirectUrl)) {
                    return Redirect::intended($redirectUrl);
                }
            }
        }
        catch(Exception $e)
        {
            Session::forget('register_user');
            $this->authorised = $this->page['authorised'] = false;
            Flash::error($e->getMessage());
        }
    }
}