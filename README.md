## Lancaster University Computer Science Society Website

This is the main site for LUCSS which is built ontop of [October](https://octobercms.com) (which in turn is built on top of [Laravel](https://laravel.com)).

### Setting up on your local machine

It's actually pretty simple, as long as you've got [Vagrant](https://vagrantup.com) installed. Use the sample `Vagrantfile` to configure your hypervisor and point `compsoc.lancs.local` to the configured IP.

Rename `.env.example` to `.env`

Once that's done, ssh into the hypervisor and run `composer install` followed by `php artisan october:up`.

Navigate to `http://compsoc.lancs.local/backend` and you can use `admin` 