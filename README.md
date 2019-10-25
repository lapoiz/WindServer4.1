Ligne de commande pour installer le projet: 

git clone https://github.com/lapoiz/WindServer4.1.git wind

cd wind

SYMFONY_ENV=prod

APP_ENV=prod 

php bin/console command_name

composer install --no-dev --optimize-autoloader

vi .env
php bin/console doctrine:schema:update --force


composer require orm-fixtures

php bin/console doctrine:fixtures:load