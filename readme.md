Laravel Reflector
===================

This is a simple Laravel 4 / Artisan package to show documentation for a given class. It loads information from the DocBlock comments on a class.


### Installation

Install the package via Composer. Edit your composer.json file to require kalani/laravel-reflector.

    "require-dev": {
        "kalani/laravel-reflector": "dev-master"
    }

Next, update Composer from the terminal:

    composer update

Finally, add the service provider to the providers array in `app\config\app.php`:

    'Kalani\LaravelReflector\LaravelReflectorServiceProvider',

Now, you should be able to use it.


### Usage

On a command line, enter:

    php artisan doc 'ClassOrFacade'

A facade name will be translated to the underlying class. For example,

    php artisan doc 'App'

is generally equivalent to:

    php artisan doc 'Illuminate\Foundation\Application'

You will not need to use quotation marks unless using a namespaced name. If you use a Facade name, the command will automatically convert it to the underlying class. 


### Output

The command will generate a simple list of available constants, properties, and methods for the given class. For instance:

```
$ php artisan doc DB

Class:  DB
Full:   Illuminate\Database\DatabaseManager
File:   C:\wamp\www\active\lkata\vendor\laravel\framework\src\Illuminate\Database\DatabaseManager.php

Properties:
- app                          The application instance.
- connections                  The active connection instances.
- extensions                   The custom connection resolvers.
- factory                      The database connection factory instance.

Methods:
  __call                       Dynamically pass methods to the default connection.
  __construct                  Create a new database manager instance.
  connection                   Get a database connection instance.
  extend                       Register an extension connection resolver.
  getDefaultConnection         Get the default connection name.
  reconnect                    Reconnect to the given database.
  setDefaultConnection         Set the default connection name.
- getConfig                    Get the configuration for a connection.
- makeConnection               Make the database connection instance.
- prepare                      Prepare the database connection instance.
```

The first character in each line indicates the visibility of the property or method:

    <none>  Public
    -       Protected
    x       Private
    *       Other

