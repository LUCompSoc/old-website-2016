## Lancaster University Computer Science Society Website

This is the main site for LUCSS which is built on top of [October](https://octobercms.com) (which in turn is built on top of [Laravel](https://laravel.com)).

### Setting up on your local machine

It's actually pretty simple, as long as you've got [Vagrant](https://vagrantup.com) installed.

#. Run `git clone https://github.com/LUCompSoc/Web.git compsoc-dev` and then `cd compsoc-dev`.
#. Point `compsoc.lancs.local` to the provided IP address in your `/etc/hosts` file.
#. Rename `.env.example` to `.env`
#. Run `vagrant up`. This uses the provided `Vagrantfile` which you may configure as you need. The first time you do this might take a while. 
#. SSH into the hypervisor with `vagrant ssh`.
#. Make sure you're in `/var/www/html`.
#. Run `composer install` to install all the dependencies into `/vendor` (this can take a while, if you just see "Killed" then give the box more memory, but 2048MB should be more than enough).
#. You'll need to create a new MySQL database called `lucss` before you can:
#. Run `php artisan october:up` to run all the migration procedures.
#. Run `php artisan key:generate` to generate a new key.
#. Navigate to `http://compsoc.lancs.local/backend` and you can use username `admin` and password `password`.

### Known caveats

* You have to change `/var/www/public` to `/var/www/html` in `/etc/apache2/sites-available/001-default.conf` on the box. Additionally remove the default scotchbox. Remember to restart apache to take effect.
* You have to 'activate' the main theme before you can see the front-end. Go to Settings > CMS > Front-end Theme and activate.
* You may experience problems front-end authentication with the university. To combat this, create a new JWT token [here](http://lancs.ac.uk/iss/jwt/list.php). Go into Settings > Users > Compsoc Profile and update the private secret and the redirect url.
* You may not be able to register new user accounts as Mattermost is not installed.