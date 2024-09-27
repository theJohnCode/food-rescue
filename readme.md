# Food Rescue Application Setup

This guide will help you clone and install a this application on your local machine.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP >= ^7.1.3 (this is the compatible version)
- Composer
- MySQL or another supported database
  
## Clone the Repository

First, clone the repository to your local machine:

```bash
git clone https://github.com/theJohnCode/food-rescue.git
```

## Change directory

```bash
cd food-rescue
```

## Install dependencies

```bash
composer install
```

## Copy .env files

```bash
cp .env.example .env
```

## Generate application key

```bash
php artisan key:generate
```

## Migrate the database
```bash
php artisan migrate
```

## Link the storage folder
```bash
php artisan storage:link
```

## Serve the application
```bash
php artisan serve
```




