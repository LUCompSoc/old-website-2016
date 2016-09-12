## Lancaster University Computer Science Society Website

This is the main site for LUCSS which is built ontop of [October](https://octobercms.com) (which in turn is built on top of [Laravel](https://laravel.com)).

### Setting up on your local machine

It's actually pretty simple, as long as you've got [Vagrant](https://vagrantup.com) installed. Use the sample `Vagrantfile` to configure your hypervisor and point `compsoc.lancs.local` to the configured IP.

Once that's done, ssh into the hypervisor and run `php artisan october:install` in the installed folder.