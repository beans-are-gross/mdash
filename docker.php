<?php
//install packages
echo "Install packages.\n";
shell_exec("apt-get update");
shell_exec("apt-get install git php-fpm php-mysql -y");

//install mdash
echo "Install mDash.\n";
shell_exec("mkdir /mdash-installer/");
shell_exec("cd /mdash-installer/ && git clone https://github.com/beans-are-gross/mdash");
shell_exec("cd /mdash-installer/mdash/ && php setup.php db_host=172.220.0.5 db_pass={$_ENV["DB_PASS"]}");
