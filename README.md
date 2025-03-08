# mDash
Reverse proxy made easy.

A web GUI controller for Caddy that automatically gives you an SSL certificate.

![](https://github.com/beans-are-gross/mdash-photos/blob/main/Home.png?raw=true)

## Features
1. Reverse proxy with a free SSL certificate from Caddy.
2. Easy to use UI, with a dashboard.
3. Multiple users can use the same mDash server.
4. You can share "apps" with other users, giving them view, or view and edit access. (Only the owner of an app can delete it.)
5. You can give users "admin" rights to allow them to delete users and bad or old login tokens.

## Docker Command Line
```
docker volume create mdash-root
docker volume create mdash-php
docker volume create mdash-caddyfile
docker network create mdash --subnet 172.220.0.0/24

docker run -d --name mdash-mysql --restart unless-stopped --network mdash --ip 172.220.0.5 -e MYSQL_ROOT_HOST=% -e MYSQL_ROOT_PASSWORD=<your-database-password> mysql
docker run -d --name mdash-installer --restart unless-stopped --network mdash -v mdash-root:/mdash/ -v mdash-php:/var/www/ -v mdash-caddyfile:/etc/caddy/ -e DB_PASS=<your-database-password> beansaregross/mdash
```

> [!IMPORTANT]
> Wait until the "mdash-installer" container exits with a status code of 143 to continue.
> If the status is not 143, please check the logs.

```
docker run -d --name mdash-php --network mdash --ip 172.220.0.10 -p 9000:9000 -v mdash-root:/mdash/ -v mdash-php:/var/www/ beansaregross/mdash-php
docker run -d --name mdash-caddy --network mdash -p 80:80 -p 443:443 -p 8080:8080 -v mdash-root:/mdash/ -v mdash-php:/var/www/ -v mdash-caddyfile:/etc/caddy/ caddy
```

## Docker Compose
### Create Volumes
```
docker volume create mdash-root
docker volume create mdash-php
docker volume create mdash-caddyfile
docker network create mdash --subnet 172.220.0.0/24
```

### Compose
```
name: mdash
services:
    mysql:
        container_name: mdash-mysql
        networks:
            mdash:
                ipv4_address: 172.220.0.5
        environment:
            - MYSQL_ROOT_HOST=%
            - MYSQL_ROOT_PASSWORD=<your-database-password>
        image: mysql
        restart: unless-stopped
    mdash:
        container_name: mdash-installer
        networks:
            - mdash
        volumes:
            - mdash-root:/mdash/
            - mdash-php:/var/www/
            - mdash-caddyfile:/etc/caddy/
        environment:
            - DB_PASS=<your-database-password>
        image: beansaregross/mdash
        restart: unless-stopped
networks:
    mdash:
        external: true
        name: mdash
volumes:
    mdash-root:
        external: true
        name: mdash-root
    mdash-php:
        external: true
        name: mdash-php
    mdash-caddyfile:
        external: true
        name: mdash-caddyfile
```

### One Time Container
This container adds the files to the volumes for the other containers to use.
```
docker run -d --name mdash-installer --restart unless-stopped --network mdash -v mdash-root:/mdash/ -v mdash-php:/var/www/ -v mdash-caddyfile:/etc/caddy/ -e DB_PASS=<your-database-password> beansaregross/mdash
```

> [!IMPORTANT]
> Wait until the "mdash-installer" container exits with a status code of 143 to continue.
> If the status is not 143, please check the logs.
>
> Then, restart the compose to update the files in Caddy.

## Server Install
Please view [terminal.md](https://github.com/beans-are-gross/mdash/blob/main/terminal.md)

The new background photo used is by [Kalen Emsley on Unsplash](https://unsplash.com/photos/green-mountain-across-body-of-water-Bkci_8qcdvQ?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash)
The old background photo used is by [Nat on Unsplash](https://unsplash.com/photos/red-and-blue-textile-on-blue-textile-9l98kFByiao?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash).