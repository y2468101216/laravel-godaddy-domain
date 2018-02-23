# Laravel Godaddy Domain

## Status

<a href="https://travis-ci.org/y2468101216/laravel-godaddy-domain.svg">
    <img src="https://api.travis-ci.org/y2468101216/laravel-godaddy-domain.svg" alt="Build Status">
</a>

## require

laravel 5 and php 7.1

## Introduce

Feel upset for static ip change to effect your domain record failure, Use this!

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

add below line in .env

```dotenv
GODADDY_KEY=your-key
GODADDY_SECRET=your-sercet
GODADDY_DOMAIN=your.domain
```

## Config

The config data order by : options > env > config > build-in value

### Usage options

```
php artisan godaddy-domain {--domain=} {--type=} {--name=} {--value=}
```

The option `domain` is you bought from godaddy

The option `type` is DNS record type, 
see : [List of DNS record types](https://en.wikipedia.org/wiki/List_of_DNS_record_types)

The option `name` is your subdomain.

The option `value` is DNS record value.

### Usage env

add below line in .env

```dotenv
GODADDY_RECORD_TYPE=your_dns_type
GODADDY_RECORD_NAME=your_dns_name
GODADDY_RECORD_VALUE=your_dns_value
```

If your use config cache, run

```
php artisan config:cache
```

### Usage config

you can set your custom default value in `config/godaddy.php`, like below

```php
<?php

return [
    'key' => env('GODADDY_KEY', 'your-key'),
    'secret' => env('GODADDY_SECRET', 'your-serect'),
    'domain' => env('GODADDY_DOMAIN', 'your-domain'),
    'type' => env('GODADDY_RECORD_TYPE', 'your-dns-record-type'),
    'name' => env('GODADDY_RECORD_NAME', 'your-dns-record-name'),
    'value' => env('GODADDY_RECORD_VALUE', 'your-dns-record-value'),
];
```

### Usage build-in

If your don't set anything, default will use build-in value or throw error below : 

* GODADDY_KEY => throw error messsage
* GODADDY_SECRET => throw error messsage
* GODADDY_DOMAIN => throw error messsage
* GODADDY_RECORD_TYPE => "A"
* GODADDY_RECORD_NAME => "www"
* GODADDY_RECORD_VALUE => default is your vps ip, get from [ipinfo.io/ip](ipinfo.io/ip)

## Feature

1. auto to buy domain that your don't have, but available to buy
2. develop no-laravel version
3. move command to class, let it can be extend. 

## License

MIT