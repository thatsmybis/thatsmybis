# <Aftershock>, a World of Warcraft: Classic Guild
    This project serves as a place to host resources and crap for the guild. Links to stuff. Links to polls. Calendars, etc. It aims to delegate all of the user management to the guild's Discord server.

    This project shouldn't become a place for active communication (ie. chatting); instead, the guild Discord should maintian that role.

    Part of the goal of this project is to make it so our members won't miss important information posted to the Discord server. Ideally, this will be done in the form of a bot that grabs important announcements from Discord and saves them onto the site. (that bot doesn't exist as of this writing - Nov 16, 2019)

    PHP was chosen as the language for this project. Why? Simply because the person who took the initiative of making this project was most familiar with PHP.

    Laravel was chosen as the framework for the project. Why? It's a very popular PHP framework with a lot of nice tools, and the guy who made this repo was familiar with it.

    (Nov 16, 2019) A CMS for Laravel hasn't been chosen yet, but one will be chosen and added in.

## Environment Setup
    The easiest way to get a local envrionment setup is with Laravel's configuration for Vagrant. Vagrant is a tool that makes it relatively painless to spin up a virtual machine containing a standardized dev environment for a specific project. This means that rather than configuring your operating system to have the appropriate packages, webserver, databases, and other requirements for this project, you just download and boot up a virtual machine that already has all that crap set up. This allows many developers to run the same dev environment, reducing troubleshooting and headaches, and putting more focus on the project itself.

    Now, there's still going to be a little bit of work and learning curve involved in getting that VM setup. But believe me, it's better than the alternative.

    Laravel has a custom environment for Vagrant. They call their environment 'Homestead', and that's what we're going to be setting up. It's easiest to get this up and running on Linux, but it's not much more work to get it running on Windows.

    Best place to start? [RTFM](https://www.urbandictionary.com/define.php?term=RTFM): https://laravel.com/docs/6.x/homestead

    tl;dr

    - Download and install VirtualBox: https://www.virtualbox.org/wiki/Downloads
    - Download and install Vagrant: https://www.vagrantup.com/downloads.html
    - Install the Homestead Vagrant 'box': `vagrant box add laravel/homestead` (works in both Windows and Linux command lines)
    - This is where I got a bit confused as the docs seem contradictory.
        - You _could_ follow the instructions, run `vagrant up`, modify the `VagrantFile` to use the correct box (laravel/homestead), and get Homestead running that way. But I don't know where/how you're supposed to configure it.
        - What I did was download/clone the Homestead repo, run `bash init.sh` (or `init.bat` in Windows), configure `Homestead.yaml`, and _then_ run `vagrant up`. If you can screw around with it and get the `Homestead.yaml` configuration working the first way, go for that I guess.
        - Lookup instructions for generating .ssh keys for either Linux or Windows, or use the ones you have.
        - A Windows configuration for `Homestead.yaml` looks like this:
            ```
            ---
            ip: "192.168.10.10"
            memory: 2048
            cpus: 2
            provider: virtualbox

            authorize: C:\Users\Joe\.ssh\id_rsa.pub

            keys:
                - C:\Users\Joe\.ssh\id_rsa

            folders:
                - map: C:\projects\aftershock-wow
                  to: /home/vagrant/code/aftershock-wow

            sites:
                - map: aftershockwow.local
                  to: /home/vagrant/code/aftershock-wow/public

            databases:
                - aftershockwow

            features:
                - mariadb: false
                - ohmyzsh: false
                - webdriver: false

            # ports:
            #     - send: 50000
            #       to: 5000
            #     - send: 7777
            #       to: 777
            #       protocol: udp
            ```
        - Make sure the hosts have been setup (read Hostname Resolution in the [docs](https://laravel.com/docs/6.x/homestead#configuring-homestead))
