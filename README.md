# Prueba Técnica - Widitrade

## Requerimientos
- Docker

## Instalación
Este proyecto se encuentra en docker, para ejecutar el proyecto, ejecutar el siguiente comando:

```
Clonar el respositorio, por defecto la carpeta sera widitrade, luego ingresar a la carpeta widitrade
cd widitrade/

Copiar el archivo .env.example a .env
cp .env.example .env

Al tener un archivo Makefile, ejecutamos los siguientes comandos para levantar el contenedor
make build

Cuando termine de construir corremos los contenedores, con el siguiente comando
make run

Instalamos las dependencias del proyecto
make composer-install

Corremos el siguiente comando para ingresar al contenedor que contiene el proyecto
make ssh-be

Al entrar en el proyecto debemos de ejecutar las migraciones
php bin/console doctrine:migrations:migrate

Ejecutamos el siguiente comando para poblar la base de datos
php bin/console app:fetch-api-data

En este punto para observar la web, debemos poner en el navegador la siguiente url
http://localhost:300

```

### Consideraciones
- Me hubiese gustado utilizar vue.js en las vistas.