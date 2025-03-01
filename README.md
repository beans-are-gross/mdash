# mdash
Reverse proxy made easy.

![](https://github.com/beans-are-gross/mdash-photos/blob/main/Home.png?raw=true)

## Features
1. Reverse proxy with a free SSL certificate from Caddy.
2. Easy to use UI, with a dashboard.
3. Multiple users can use the same mDash server.
4. You can share "apps" with other users, giving them view, or view and edit access. (Only the owner of an app can delete it.)
5. You can give users "admin" rights to allow them to delete users and bad or old login tokens.

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
```

```
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

The background photo used is by [Nat on Unsplash](https://unsplash.com/photos/red-and-blue-textile-on-blue-textile-9l98kFByiao?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash).
