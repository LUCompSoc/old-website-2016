<?php namespace Compsoc\User\Components;

use DB;
use Auth;
use Mail;
use Lang;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Models\Settings as UserSettings;
use Compsoc\User\Classes\JWT;
use Compsoc\User\Classes\Mattermost;
use Compsoc\User\Models\Settings;
use Exception;
use ApplicationException;

class RegisterForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'compsoc.user::lang.register.name',
            'description' => 'compsoc.user::lang.register.desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'compsoc.user::lang.register.redirect_to',
                'description' => 'compsoc.user::lang.register.redirect_to_desc',
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

            $request_register = Session::get('request_register');
            $payload = Session::get('register_user');
            $this->page['authorised'] = false;

            if(!$payload || is_null($payload))
            {
                // TODO:
                // Implement pay wall to help with funding??
            }
            else if (count(array_intersect(['displayName', 'username', 'mail'], array_keys((array)$payload))) < 3)
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.login.error.bad_request'));
            }
            else if($user = UserModel::findByEmail($payload->mail))
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.register.error.already_registered'));
            }
            else
            {
                $this->page['authorised'] = true;

                $this->addJs('/plugins/compsoc/user/assets/js/register.js');

                $this->page['display_name'] = $payload->displayName;
                $this->page['username'] = $payload->username;
                $this->page['email'] = $payload->mail;
            }
        }
        catch(Exception $e)
        {
            // Session::forget('register_user');
            $this->authorised = $this->page['authorised'] = false;
            Flash::error($e->getMessage());
        }
    }

    public function onRegister()
    {
        try
        {
            $return = [];
            Flash::purge();

            /*
             * Validate input
             */
            $post = post();
            $payload = Session::get('register_user');
            $this->page['registerSuccess'] = false;

            // Is any required data missing?
            if(count(array_intersect(['display_name', 'username', 'email'], array_keys($post))) < 3)
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.register.error.bad_request'));
            }

            // Trying to 'Inspect Element' huh?
            else if ($post['email'] !== $payload->mail || $post['username'] !== $payload->username || $post['display_name'] !== $payload->displayName)
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.register.error.bad_request'));
            }

            // Is this user registered?
            else if ($user = UserModel::findByEmail($payload->mail))
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.register.error.already_registered'));
            }

            // Missing Mattermost password?
            // TODO: Put minimum password length into a config somewhere
            else if(isset($post['auto_irc']) && (int)$post['auto_irc'] == 1 && (!isset($post['irc_password']) || strlen($post['irc_password']) < 12 || !preg_match('/[A-Z]+[a-z]+[0-9]+/', $post['irc_password'])))
            {
                throw new ApplicationException(Lang::get('compsoc.user::lang.register.error.irc_password', [ 'num' => 12 ]));
            }

            // Not registered and data ok, cool!
            else
            {
                $irc_id = null;

                /*
                 * Register user on Mattermost
                 */
                if(isset($post['auto_irc']) && (int)$post['auto_irc'] == 1) {
                    Mattermost::create_user($payload->username, $payload->mail, $post['irc_password']);
                    $result = DB::connection('mattermost')->select('select Id from Users where Email = :email limit 1', ['email' => $payload->mail]);
                    $irc_id = $result[0]->Id;
                }

                /*
                 * Register user on this system
                 */
                $names = explode(' ', $payload->displayName);
                // No actual password since we are using JWT
                $password = md5(time() + uniqid());
                $data = [
                    'email' => $payload->mail,
                    'username' => $payload->username,
                    'password' => $password,
                    'password_confirmation' => $password,
                    'name' => $names[0],
                    'surname' => implode(' ', array_slice($names, 1)),
                    'university_id' => (int)$post['library_card_number'],
                    'union_id' => (int)$post['lusu_number'],
                    'irc_id' => $irc_id,
                    // TODO: Check if posted position is real position -- maybe via dynamic list
                    'position' => $post['position'],
                    'title' => isset($post['title']) ? strip_tags(trim($post['title'])) : ''
                ];

                $requireActivation = UserSettings::get('require_activation', true);
                $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
                $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
                $user = Auth::register($data, $automaticActivation);

                Session::set('request_register', null);
                Session::set('register_user', null);

                /*
                 * Did the user want to subscribe to the newsletter?
                 */
                if(isset($post['newsletter']) && $post['newsletter'] == 1)
                {
                    if (DB::table('news_subscribers')->where('email', $payload->mail)->count() == 0)
                    {
                        DB::table('news_subscribers')->insert([
                            'name'       => $payload->displayName,
                            'email'      => $payload->mail,
                            'common'     => '',
                            'created'    => 2,
                            'statistics' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                /*
                 * Activation is by the user, send the email
                 */
                if ($userActivation) {
                    Flash::success(Lang::get('rainlab.user::lang.account.activation_email_sent'));
                    $this->sendActivationEmail($user);
                    $return['.form-horizontal'] = '';
                }

                /*
                 * Automatically activated or not required, log the user in
                 */
                if ($automaticActivation || !$requireActivation) {
                    Auth::login($user);

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
        }
        catch(Exception $e)
        {
            $this->page['authorised'] = false;
            Flash::error($e->getMessage());
        }

        return array_merge($return, 
            ['#flash' => $this->renderPartial('registerForm::flash-messages')]
        );
    }

    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);
        $link = $this->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }
}