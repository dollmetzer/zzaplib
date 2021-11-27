zzaplib
=======

Zzaplib is a lightweight PHP application framework.
The roots of this library are a now defunct messaging application named zzap
in 2006-2009.
The library was under constant development since then for various web applications.

Main Goals
----------

- Easy maintainable MVC structure
- lean code, high speed and low memory usage
- support multiple sites, multiple languages and multiple devices

Installation using composer
---------------------------
Create a file named composer.json in your project directory and
insert the following code:

    {
        "name": "zzapapp",
        "require-dev": {
            "dollmetzer/zzaplib": "3.0.x-dev"
        },
        "repositories": [
            {
                "type": "vcs",
                "url": "git://github.com/dollmetzer/zzaplib",
                "reference": "3.0"
            }
        ],
        "autoload": {
            "psr-4": {
                "Application\\": "app"
            }
        }
    }

Then run on the command line:

    php composer.phar install

Create a new application
------------------------
See the scaffold / example application zzapapp on github:

https://github.com/dollmetzer/zzapapp