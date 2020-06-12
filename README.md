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
1. `make build` to install everything
2. `make serve` to start application

Without any other change, app will be served on `localhost:8970` 


### PhpMyAdmin
Accessible on `localhost:8970` by default. Use `MYSQL_USER` and `MYSQL_PASSWORD` to connect.