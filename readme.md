This project is a skeleton for quickly standing up a local HTTP and DB environment through Docker:

- HTTP has separate dev and test servers
    - built on Apache and PHP 7.2
    - application powered by Symfony 4.3
    - test suite powered by Codeception 3.1
- DB has separate dev and test servers
    - built on MySQL 5.7

# Getting Setup

First, you'll need to get some local environment things out of the way:

1. use the setup script to copy core files to your project
    - run `php setup.php -app app-name destination`
        - `app-name` is the name of your app. this will be used for naming Docker project, host names, and a few other things
        - `destination` is the path to your project
2. review `_infrastructure/.env` and change variables as needed
    - make note of `APP_DEV_HTTP_HOST` and `APP_DEV_HTTP_PORT` -- you'll use those below
3. install Docker
    - run `brew cask install docker`
4. install DNSMasq (allows you to easily access `$domain.localhost`):
    - run the following
    - ```
      brew install dnsmasq
      echo "address=/localhost/127.0.0.1" >> /usr/local/etc/dnsmasq.conf
      sudo mkdir -p /etc/resolver
      sudo tee /etc/resolver/localhost >/dev/null <<EOF
      nameserver 127.0.0.1
      EOF
      sudo brew services start dnsmasq
      ```

Last, run these commands:

1. `cd _infrastructure`
2. `./build_image.sh db`
3. `./build_image.sh http [version]`
    - `version` is the PHP version to install. Default is `7.4`; `7.1, 7.2, 7.3` are other acceptable values
    - note that you might have to update composer's SHA -- just copy from https://getcomposer.org/download/
4. `docker-compose up`

If all goes well, you should be able to go to `http://${APP_DEV_HTTP_HOST}:${APP_DEV_HTTP_PORT}` and see your PHP settings. That's it! Now you can customize away.

# Future

- add integration/instructions for CircleCI
