# My Yousign GH Challenge implementation

Based on [this stack](https://github.com/compagnie-hyperactive/docker-boilerplate-symfony).

Serve 2 main goals :
- Import _via_ Symfony command GH data, scoped on one day.
- Serve data _via_ API.

## Installation / First launch

`Makefile` eases installation. Basically, do : 
1. Create a `.env.local` file with your host user data, such as
```
HOST_USER=nicolas
HOST_UID=1000
HOST_GID=1000
```
2. `make build` to install everything needed for containerization
3. `make composer-install` speaks for itself
4. `make migrate` to execute migrations

### PhpMyAdmin
Accessible on `localhost:8971` by default. Use `MYSQL_USER` and `MYSQL_PASSWORD` to connect.

## Use application

### Start application
`make serve` to start application

Without any other change, app will be served on `localhost:8970` 

### Import part

1. `make bash-php` for access bash on PHP container
2. Execute command such as `php bin/console app:import:github_events --day=20200613 --hour=14 -e prod` to trigger
Github event import for the 13/06/2020 happened between 14h and 15h.

### API
TODO

## Design considerations
TODO
