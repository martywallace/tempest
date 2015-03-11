# Tempest.

Tempest is a tiny PHP framework for developing small to medium sized websites and applications. It is a lightweight alternative to heavier frameworks like [Laravel](http://laravel.com/); for developers who only need basic routing and general project structure to get going. Tempest also includes some useful tools for front-end development; automatic TypeScript, JavaScript and SASS compilation and minifaction to name a few.

## Pre-requisites.

Tempest makes use of various tools to streamline the development process:

* [Composer](https://getcomposer.org/), for managing PHP dependencies.
* [NodeJS & Node Package Manager](http://nodejs.org/), for managing Grunt tasks.
* [Bower](http://bower.io/), for managing front-end JavaScript dependencies.
* [Grunt](http://gruntjs.com/), for running Grunt tasks.
* [Ruby](http://rubyinstaller.org/), required by SASS.
* [SASS](http://sass-lang.com/install), for compiling SASS.
* [TypeScript](http://www.typescriptlang.org/#Download), for compiling TypeScript.

## Getting Started.

1. Download and extract the files to your server - `public` should be set as the server root.
	* Set up a virtual host; [these instructions](http://sawmac.com/xampp/virtualhosts/) may help beginners, or;
	* Use PHP's [inbuilt server](http://php.net/manual/en/features.commandline.webserver.php). Simply navigate to the application root, then:

			$ php -S localhost:80 -t public

2. `cd` to your application directory and run the following commands:

		$ npm install
		$ composer install
		$ bower install
		$ grunt

## Development Guide.

### Grunt.

[Grunt](http://gruntjs.com/) is used to automate the compilation of various files into the public directory (e.g. `/sass` into `/public/css`). There are two grunt tasks available:

1. `grunt dev` (default) - compiles files without minification and runs a watch task for use during development.
2. `grunt prod` - compiles and minifies files for production use.

### JavaScript & TypeScript.

* Put your JavaScript or TypeScript files into `js/app/`. They will be compiled down into `public/js/app.js`.
* For JavaScript dependencies;
	* Install via `bower` or download and place them into `js/vendor/`.
	* Append required dependencies to the `Gruntfile` under `config.vendorJs`.
	* These will be cocatenated down into `public/js/vendor.js`.
* Tempest comes with some JavaScript of its own. It is compiled down into `public/js/tempest.js`.

### SASS.

* Put your SASS into `sass/`. Your SASS will be compiled down into `public/css/`.
* Files beginning with `_` will not be compiled, following standard SASS rules.
* Some useful boilerplate SASS is located in `sass/vendor/`.

### HTML & Twig.

* Tempest uses [Twig](http://twig.sensiolabs.org/) for templating.
* Put your Twig & HTML files in `html/`.
* These files are not manipulated or compiled in any way; Tempest will look in `html/` for Twig templates.

### Configuration.

* Application configuration is stored in `config.php`. More instructions on configuration are provided at the top of the file.

## Releases.

* [1.0.0](https://github.com/MartyWallace/Tempest/releases/tag/1.0.0)
	* Modern project structure (using `public` for public files).
	* Using Twig for templating.
	* Added Grunt for task automation.
	* Added SASS & TypeScript compilation.
	* Added Bower for JavaScript dependency management.
	* Added Composer for PHP dependency management.
	* Application services.
	* Cascading app configuration based on server name.
	* General cleanup.

* [0.0.1](https://github.com/MartyWallace/Tempest/releases/tag/0.0.1)
	* This release is explained in the [wiki section](https://github.com/MartyWallace/Tempest/wiki).
    * Custom logicless templating.
    * Easy to set up (no need for Composer, Grunt, virtual host setup, etc).
    * Runs correctly in subdirectories as its own application.

## License.

Copyright 2015 Marty Wallace. This is a free & open source project licensed under [MIT](http://opensource.org/licenses/MIT).