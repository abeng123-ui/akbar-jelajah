# Project Specification
- XAMPP for Windows 7.0.16
- MYSQL
- Laravel 5.5
- Sqlite for Testing Database
- Postman

# Unit Test Requirement
- Install sqlite at php.ini

# How to deploy project
- I am using XAMPP, at the htdocs folder, open git bash/cmd terminal
- git clone https://github.com/abeng123-ui/akbar-jelajah
- cd akbar-jelajah
- create database
- set up .ENV to config database
- composer install
- php artisan migrate
- php artisan passport:install

# How to Execute Unit Test
- vendor/bin/phpunit

# Upload collection into Postman
- Import file named "Akbar Jelajah.postman_collection.json"
- There is 3 folders, User for registering user, News for CRUD News, and Comments for CRUD Comments

# How to register User (from Postman)
- Hit Endpoint Register POST {{url}}/akbar-jelajah/public/api/register, and fill name, email, password,
c_password, and role (fill it with admin or user)
- Hit Endpoint Login POST {{url}}/akbar-jelajah/public/api/login, and copy 'token' value, to get your access token
- To create News, Login as admin then hit Endpoint POST {{url}}/akbar-jelajah/public/api/news, fill headers Authorization with 'Bearer {{your_access_token}}'
- To create Comments, Login as user then hit Endpoint POST {{url}}/akbar-jelajah/public/api/comment, fill headers Authorization with 'Bearer {{your_access_token}}'
- To get News details, hit Endpoint GET {{url}}/akbar-jelajah/public/api/news


