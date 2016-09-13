## Lancaster University Computer Science Society Website

This is the main site for LUCSS which is built on top of [October](https://octobercms.com) (which in turn is built on top of [Laravel](https://laravel.com)).

### Setting up on your local machine

It's actually pretty simple, as long as you've got [Vagrant](https://vagrantup.com) installed.

1. Run `git clone https://github.com/LUCompSoc/Web.git compsoc-dev` and then `cd compsoc-dev`.
2. Point `compsoc.lancs.local` to the provided IP address in your `/etc/hosts` file.
3. Rename `.env.example` to `.env`
4. Run `vagrant up`. This uses the provided `Vagrantfile` which you may configure as you need. The first time you do this might take a while. 
5. SSH into the box with `vagrant ssh`.
6. Make sure you're in `/var/www/html`.
7. Run `composer install` to install all the dependencies into `/vendor` (this can take a while, if you just see "Killed" then give the box more memory, but 2048MB should be more than enough).
8. You'll need to create a new MySQL database called `lucss` before you can:
9. Run `php artisan october:up` to run all the migration procedures.
10. Run `php artisan key:generate` to generate a new key.
11. Navigate to `http://compsoc.lancs.local/backend` and you can use username `admin` and password `password`.

### Known caveats

* You have to change `/var/www/public` to `/var/www/html` in `/etc/apache2/sites-available/001-default.conf` on the box. Additionally remove the default scotchbox. Remember to restart apache to take effect.
* You have to 'activate' the main theme before you can see the front-end. Go to Settings > CMS > Front-end Theme and activate.
* You may experience problems with front-end authentication to the university. To combat this, create a new JWT token [here](https://weblogin.lancs.ac.uk/jwt/list.php). Go into Settings > Users > Compsoc Profile and update the private secret and set redirect url to `https://weblogin.lancs.ac.uk/jwt/<name>`.
* You may not be able to register new user accounts as Mattermost is not installed.
* Mattermost's `platform` binary and `data` and `log` directory need to have the `www-data` group assigned to it.