# Sample Project
===================

This Application based on the php, nginx and sqlite as database engine.

## Requirements

* [docker](https://docs.docker.com/get-docker/)
* [docker-compose](https://docs.docker.com/compose/install/)

## Installation
This installation guide expects that you're using docker-compose.

### 1. Start containers

For this just run following command:
```bash
docker-compose up -d --build
```

### 2. Using application

Connect to the php container container:
```bash
docker-compose exec php bash
```

Run following command to generate SSH keys:
```bash
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

Copy passphrase and past it to your .env.local file

### 2. Create .env.local

Create .env.local based on the .env file template.
Pass passphrase from the previous step to the `JWT_PASSPHRASE` variable.


## Usage

1. Open `/` url to see the full list of available actions.
2. Register new user with `username` and `password` on the `/api/register` route.
3. Get JWT token for new user on the `/api/login_check` route.
4. Pass the token to the Authorize section of the NelmioApiDoc.
5. Enjoy.


## Tests

Application has two types of tests:
- Unit
- Functional

### 1. Running Unit tests
```bash
vendor/bin/phpunit --testsuite Unit
```

### 2. Running Functional tests

Before this you need to create test DB as Functional tests use database.

```bash
bin/console doctrine:database:create -e test
bin/console doctrine:schema:create -e test
```
First command creates database based on parameters from `.env.test`.
Second command creates schema. 

Command to run function tests only:
```bash
vendor/bin/phpunit --testsuite Functional
```

## Useful commands

`make cache-clear` - Runs clear cache command
`make cs-check` - Runs PHP-CS-Fixer in dry run mode
`make cs-fix` - Runs PHP-CS-Fixer and fix all possible issues.
`make` - Shows available commands 
