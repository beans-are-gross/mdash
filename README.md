# mdash
The simple reverse proxy built with Caddy.

## Setup

### Step 1 - Install
The following packages are required:
1. MySQL
2. Git
3. PHP-FPM
4. PHP-MySQL Plugin

> [!NOTE]
> You can use your existing MySQL installation if it's on the same server as mDash.

```
sudo apt update
sudo apt install mysql-server git php-fpm php-mysql -y
```

### Step 2 - Setup the Database
> [!NOTE]
> If you have an existing MySQL installation, you can skip these steps.

```
mysql
```

> [!IMPORTANT]
> Dont forget to enter the new password you would like!

```
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '<new-pass>';
```

```
FLUSH PRIVILEGES;
exit
```

### Step 3 - Download mDash
```
git clone https://github.com/beans-are-gross/mdash
cd mdash
```

### Step 4 - Install mDash
```
sudo php setup.php db_pass=<your-database-password>
```
