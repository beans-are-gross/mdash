mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DATABASE_PASSWORD}';"
mysql -e "FLUSH PRIVILEGES;"