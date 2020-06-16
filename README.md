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
4. `make migrate` to execute migrations on database

### PhpMyAdmin
Accessible on `localhost:8971` by default. Use `MYSQL_USER` and `MYSQL_PASSWORD` to connect.

## Use application

### Start application
`make serve` to start application

Without any other change, app will be served on `localhost:8970` 

### Import part

1. `make bash-php` for access bash on PHP container
2. Execute command such as `php bin/console app:import:github_events --day=20200613 --hour=14 -e prod` to trigger
Github events import for the 13/06/2020 happened between 14h and 15h.

### API
TODO

## Design considerations
This challenge is splitted in 2 parts : import command and small API. My internal guidelines : 
balance between __only doing what was asked__ but __keeping a way to make evolve__.

That's why I chose to persist only __commits__ and __repo__, and internalize some event related data 
directly into commit (domain definition assertion).

### Import command
I tried to make something as efficient as I could. Below some speed enhancements :
- Garbage collector manual calls
- Unset variables uneeded anymore
- Doctrine adjustments for performances (degrading logging data)

As I wanted to keep a loose coupling between import process and persistence : 
- I used DTOs coupled with Symfony serialize component to stick closely to the data I got in file.
- I fired an event in command that allow listeners to hook on. For now, only pushes event listener is made.

This approach could lever a future asynchonous handling (using RabbitMQ or some async queue)
