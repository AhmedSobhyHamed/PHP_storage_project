# PHP_storage_project

Websit can store vedios, images, stories or books details and daily notes for users.

![](repo-image/name.png)

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [License](#license)
- [Contact](#contact)

  <!-- intro -->

## Introduction

This project craeted using vanilla PHP, vanilla JavaScript and Bootstrap css framwork to create a user interface for storing vedios, images, stories or books details and daily notes.
<br>
To use this storage system you must be registered and sign in or regester a new account.
<br>
every user can see his own resources only.
<br>
you can edit any esources when you wish and even delete it.
<br>
you can create a new resource of type you want.

  <!-- technologies was used with links if available -->

## Features

```diff
- PHP
  - Using pre-made PHP library.
  - Separate html from PHP.
  - Easy change content of html template with php.
  - Class for file handeler with check validation for size and type.
  - Class for session handeling.
  - Protect against Session hajaking.
  - Protect against Session fixation.
  - mainupolate session sittings from PHP script.
  - Using coockies.
  - create interface for DataBase.
  - Separate Database routines inside clasess for user and resources.
  - adopt Object Relational Model approch when creating resources and user classes.
  - using mySQLi interface to connect to database.
  - Use hashing and encrypt
  - Validate data for every database resource model.
  - Prevent sql injection.
  - Using regular expression.
  - Create custon non-standerd API.
- JavaScript
  - Async communication using xmlhttprequest.
  - Event handling.
  - DOM manipulate.
  - Create custom media api.
  - Form validating.
  - Using regular expression.
- SQL
  - create a user with a restricted permission.
  - full CRUD system.

```

  <!-- get start and how to run with the prerequisites mintion -->

## Installation

1. install pre-requisies utilities

   - install php
     on Ubuntu, Debian, and Linux Mint:

     ```sh
     sudo apt-get install php8.1 php8.1-cli php8.1-common php8.1-curl php8.1-mysql
     ```

   - install mysql
     on Ubuntu, Debian, and Linux Mint:

     ```sh
     sudo apt-get install mysql-client-8.0 mysql-client-core-8.0 mysql-server-core-8.0
     ```

   - install apache
     on Ubuntu, Debian, and Linux Mint:

     ```sh
     sudo apt-get install apache2
     ```

2. Clone the repository:

   ```sh
   git clone https://github.com/AhmedSobhyHamed/Laravel_EasyT_project2.git
   ```

3. Navigate to the project directory:

   ```sh
   cd work_directory
   ```

4. Create Database and User:

   - open mysql

     ```sh
     sudo mysql
     ```

   - create database

     ```sh
     CREATE DATABASE card_site;
     ```

   - create user

     ```sh
     CREATE USER IF NOT EXISTS
     'cardsite'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'password'
     REQUIRE NONE
     WITH MAX_CONNECTIONS_PER_HOUR 1800 MAX_USER_CONNECTIONS 5
     PASSWORD EXPIRE NEVER FAILED_LOGIN_ATTEMPTS 5 PASSWORD REQUIRE CURRENT PASSWORD_LOCK_TIME 1;
     ```

   - grant privilages

     ```sh
     GRANT INSERT,UPDATE,DELETE,SELECT,CREATE,DROP,ALTER,REFERENCES ON `card_site`.* TO 'cardsite'@'localhost';
     FLUSH PRIVILEGES;
     ```

5. Create DB info file :

   ```sh
   cp DB_info.example.php  DB_info.php
   ```

6. Runing apache

   ```sh
   sudo systemctl start apache2
   ```

   Then open the browser to localhost.

  <!-- usage or how to interact with this technologies like api end points and what they do -->

## Usage

<!-- **You can interact with the project via this link**
[web page on github](https://ahmedsobhyhamed.github.io/Laravel_EasyT_project2/).
<br> -->

**[see an example for user interaction.[video]](http://youtube.com)**

## Technologies Used

- languages:

  - PHP
  - mysql
  - JavaScript
  - HTML 5.
  - CSS.

- framworks and Libraries:

  - Bootstrap.
  - mysqli interface.
  - PHP_HTML-manipulator.

    <!-- about the project and a digram of how it work -->

## Project Structure

![](repo-image/php.png)

  <!-- licance -->

## License

This project is licensed under the MIT License - see the [LICENSE](/LICENSE) file for details

  <!-- contacts -->

## Contact

Created by [Ahmed Sobhy]:

- email: [ahmed.s.abdulaal@gmail.com](mailto:ahmed.s.abdulaal@gmail.com)
- linkedin: [Ahmed Sobhy](https://www.linkedin.com/in/ahmed-sobhy-b824b7201/)
  <br>
  feel free to contact me!
