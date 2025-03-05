FROM ubuntu

RUN apt-get update && apt-get install git php-fpm php-mysql -y
RUN mkdir /mdash-installer

WORKDIR /mdash-installer/

RUN git clone https://github.com/beans-are-gross/mdash

WORKDIR /mdash-installer/mdash/

CMD ["php", "setup.php", "docker=true"]