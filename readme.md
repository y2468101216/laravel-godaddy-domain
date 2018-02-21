# Laravel Godaddy Domain

## Status

<a href="https://travis-ci.org/y2468101216/laravel-godaddy-domain.svg">
    <img src="https://api.travis-ci.org/y2468101216/laravel-godaddy-domain.svg" alt="Build Status">
</a>

## require

laravel 5 and php 7.1

## Introduce

Feel upset for static ip change to effect your domain record Failure, use this!

## Install

Install via composer

```
composer install y2468101216/laravel-godaddy-domain
```

add service provider to `config/app.php` in `providers` block (laravel 5.4 or lower need)

```
Y2468101216\Godaddy\DomainServiceProvider::class
```

publish config

```
php artisan vendor:publish \
 --provider="Y2468101216\Godaddy\DomainServiceProvider"
```