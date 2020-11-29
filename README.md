# Laravel Nova Currency Tracker

## Overview
A simple  Laravel Currency Tracker using Nova custome tools

### Installation

### clone project 
```
git clone laravel-currency-tracker-nova
```
### install dependency 
```
cd laravel-currency-tracker-nova && composer install 
```

### copy env
```
cp .env.example .env
```

### database
Here in this project i used sqlite but you can use what fit to you
```
touch database/database.sqlite
```

### migrate database
```
php artisan migrate
```

### create a user 
```
php artisan nova:user
```

#### run web  app

```
php artisan serve
```


