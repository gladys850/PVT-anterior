# DOCKERIZANDO EL PROYECTO

## Requisitos

* [Docker](https://docs.docker.com/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* Conexión a internet

## Configuración

* Una vez clonado el proyecto, dentro del directorio raíz del mismo se debe clonar el submódulo Laradock:

```sh
git submodule update --init --recursive
```

* Copiar las recetas modificadas:

```sh
cp -f docs/docker/docker-compose.yml laradock/
cp -f docs/docker/env-example laradock/.env
```

* Ingresar al directorio laradock:

```sh
cd laradock
```

* Modificar el archivo `.env` de laradock de acuerdo a los puertos que se irán a utilizar.

* Generar las imágenes:

```sh
docker-compose build --parallel nginx php-fpm workspace laravel-echo-server redis postgres
```

* Levantar los contenedores:

```sh
docker-compose up -d nginx laravel-echo-server postgres
```

## Instalar las dependencias

* Verificar que los contenedores se encuentren funcionando:

```sh
docker-compose ps
```

* Instalar las fuentes dentro del contenedor `php-fpm`:

```sh
docker-compose exec workspace /var/www/install-roboto-fonts.sh
```

* Instalar el soporte para lenguaje español:

```sh
docker-compose exec php-fpm /var/www/install-spanish-locale.sh
docker-compose exec workspace /var/www/install-spanish-locale.sh
```

* Dentro del contenedor workspace, generar las variables de entorno:

```sh
docker-compose exec --user laradock workspace composer run-script post-root-package-install
```

* Instalar las dependencias:

```sh
docker-compose exec --user laradock workspace composer install
```

* Generar las llaves de sesión y de autenticación:

```sh
docker-compose exec --user laradock workspace composer run-script post-create-project-cmd
```

* Cambiar el modo de laravel-echo-server a producción

```sh
docker-compose exec laravel-echo-server sed -i 's/\"devMode\":.*/\"devMode\": false,/g' laravel-echo-server.json
docker-compose restart laravel-echo-server
```

* Modificar el archivo `.env` de laravel de acuerdo a las credenciales de base de datos, sockets, etc.

* Transpilar el código Javascript

```sh
docker-compose exec --user laradock workspace yarn prod
```

## Para continuar con el desarrollo

* Cambiar la variable APP_ENV=development en el archivo `.env` de laravel y transpilar el código Javascript:

```sh
docker-compose exec --user laradock workspace yarn dev
```

## Problemas comúnes

* Si se encuentran problemas en la console de error, verificar los contenedores en estado `exited`:

```sh
docker-compose ps
```

* Eliminar los contenedores que no se encuentren levantados:

```sh
docker rm every_unused_container
```

* En caso de seguir con problemas, eliminar todos los contenedores, imágenes, redes y volúmenes que no se encuentren en uso:

```sh
docker container prune
docker image prune -a
docker network prune
docker volume prune
```

* Volver a generar las imágenes desde cero.