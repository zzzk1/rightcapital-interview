## 环境配置

### Ubuntu 22.04 上配置 PHP 框架 Laravel 

#### 1. 更新系统包，保证所有的系统包都是最新的

```shell
sudo apt update
sudo apt upgrade
```
---
#### 2. 安装 PHP 以及扩展包并验证

仅仅添加当前项目会用的扩展包，有需求的时候再加

- [php-common](https://stackoverflow.com/questions/23295393/what-is-php-common-and-what-does-it-do)：PHP 通用文件包，包含了绝大多数的基础功能和配置
- php-mysqlnd：用于与 MySQL 进行连接交互
- php-curl：PHP 的 cURL 扩展，这里用来发送测试的 HTTP 请求

```shell
//这是第一条命令, 用于安装
sudo apt install php php-common php-cli php-mysqlnd php-curl

//这是第二条命令, 用于检查安装结果
php -v
```

成功的截图如下：

![](./assets/php-v.png)

---

#### 3. 安装 PHP Composer

安装 Laravel 依赖项需要 [Composer](https://getcomposer.org/doc/00-intro.md)(Java开发中相当于 Maven)

```sh
//这是第一条命令, 用于安装
curl -sS https://getcomposer.org/installer | 
sudo php -- --install-dir=/usr/bin --filename=composer

//这是第二条命令, 用于检查安装结果
composer --version
```

安装完成：

![](./assets/composer.png)

测试一下：

![](./assets/verfity-composer-successful.png)

---

#### 4. 创建一个 Composer 项目

> hello_laravel 是项目名

```shell
composer create-project laravel/laravel hello_laravel
```

> **[php xml 这个扩展在 Ubuntu 中默认没有启用](https://stackoverflow.com/questions/68873115/require-ext-xml-it-is-missing-from-your-system-install-or-enable-phps-xml)**
>
> ![](./assets/composer-create-project-failed-xml.png)

```sh

apt-get install php-xml

// 再试一次
composer create-project laravel/laravel hello_laravel
```

> 再次创建项目的时候记得删掉上面创建失败的
>
> ![](./assets/create_before_deleted.png)

![](./assets/build-project-successful.png)

#### 5.启动框架

##### 5.1 命令行进入项目内

```sh
cd hello_laravel
ls
```

![](./assets/cd_project_and_ls.png)

##### 5.2 启动服务器 并暴露端口

```
php artisan serve --host 127.0.0.1 --port 8000
```

![](./assets/start-server-8000.png)

##### 5.3 使用Web浏览器访问



![](./assets/client-server-200.png)
