## Environment
Run `docker-compose build` and `docker-compose up -d` in the project folder, then use
`docker exec -it <name_of_php_container> /bin/bash` to enter the PHP container for composer install, migration and running the unit-test.

## Dependencies

In php container;

Run `composer install`

## Import Data

In php container;

Run `php bin/console doctrine:database:create` to create DB

Run `php bin/console doctrine:migrations:migrate` to create tables

Run `php bin/console csv:import` to import data

## Call Api

You can find a postman export file on public folder

## Unit Test

In php container;

`php bin/phpunit`
