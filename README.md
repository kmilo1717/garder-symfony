# Garden app - Symfony

This project in symfony v5.4 is about garden managment, it can save and edit students, also it can export data to excel.


Requirements:
- PHP 8.1
- Composer
- MySQL

## Instalation and configurarion

Clone this repository and enter using cd, and then use

```bash
composer install
```
Then, create a db in your postgresql named "gardendb" (default) or you preferences, if you chance it, you must change in .env file

The next step is migrate all database using the ORM, run this comand

```bash
php bin/console make:migration
```
and

```bash
 php bin/console doctrine:migrations:migrate
```
Those will generate a version file of db and will run the scripts



## Development server

To start a local development server, there are 2 ways:


## Option 1 - Symfony CLI  (recomended) :

- Step 1: Install scoop
Run those commands in powershell
```bash
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Invoke-RestMethod -Uri https://get.scoop.sh | Invoke-Expression
```
- Step 2: Install Symfony CLI using scoop
Run this command on cmd or powershell
```bash
scoop install symfony-cli
```
- Step 3: Run the project with Symfony CLI
```bash
cd garder-symfony/
symfony server:start
```

Go to http://127.0.0.1:8000

## Option 2 - Laragon or whatever app you feel comfortable:

This option depends on the app, but normally you have to run env and copy and past in public file. 
