# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "scotch/box"
    config.vm.network "public_network", ip: "192.168.1.180"

    config.vm.hostname = "lucss"
    config.vm.synced_folder ".", "/var/www/html", :mount_options => ["dmode=777", "fmode=666"]

    config.vm.provider "virtualbox" do |v|
        v.memory = 512
        v.cpus = 1
    end

end
