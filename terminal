# Terminal Installation

## Step 1 - Install
The following packages are required:
1. MySQL
2. Git
3. PHP-FPM
4. PHP-MySQL Plugin

> [!NOTE]
> You can use an existing MySQL installation on another server.

```
sudo apt update
sudo apt install mysql-server git php-fpm php-mysql -y
```

## Step 2 - Setup the Database
> [!NOTE]
> If you have an existing MySQL installation, you can skip these steps.

```
mysql
```

> [!IMPORTANT]
> Dont forget to enter the new password you would like!

```
ALTER USER 'root'@'localhost' IDENTIFIED WITH caching_sha2_password BY '<new-pass>';
```

```
FLUSH PRIVILEGES;
```

```
exit
```

## Step 3 - Download mDash
```
git clone https://github.com/beans-are-gross/mdash
cd mdash
```

## Step 4 - Install mDash
> [!NOTE]
> If you have another server running MySQL, change "db_host".
```
sudo php setup.php db_host=127.0.0.1 db_pass=<your-database-password>
```
