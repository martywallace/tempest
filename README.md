# Tempest.

Tempest is a tiny PHP framework for developing small to medium sized websites and applications. It is a lightweight alternative to heavier frameworks like [Laravel](http://laravel.com/); for developers who only need basic routing and general project structure to get going.


## Getting Started.

1. Download and extract the files to your server - `public` should be set as the server root.
	* Set up a virtual host; [these instructions](http://sawmac.com/xampp/virtualhosts/) may help beginners, or;
	* Use PHP's [inbuilt server](http://php.net/manual/en/features.commandline.webserver.php). Simply navigate to the application root, then:

			$ php -S localhost:80 -t public

2. `cd` to your application directory and run the following commands:

		$ composer install

## HTML & Twig.

* Tempest uses [Twig](http://twig.sensiolabs.org/) for templating.
* Put your Twig & HTML files in `html`.

## Configuration.

* Application configuration is stored in `/config/*.php`. More instructions on configuration are provided at the top of the file.

## Releases.

* [1.3.0](https://github.com/MartyWallace/Tempest/releases/tag/1.3.0)
	* Split site configuration into multiple files.
	* Can define an alternate site root.
	* Added Modernizr & Browser detection kit.
	* `www.` ServerAlias is still matched when cascading configuration.
	* Minor bug fixes around competing SASS & TypeScript compilation.
	* Added various Twig functions and filters.

* [1.2.0](https://github.com/MartyWallace/Tempest/releases/tag/1.2.0)
	* Renamed Responder to Controller.
	* Added ability to pass arbitrary data to controllers via route definitions.
	* Added behaviour where Tempest will look for templates with a file-name matching the request URI if no route definitions matched.

* [1.1.0](https://github.com/MartyWallace/Tempest/releases/tag/1.1.0)
	* Greatly improved error handling; you can now handle specific HTTP errors buy placing a Twig template with the same name into `/html/_status/`.
	* Cleaned up templates.
	* Cleaned up SASS.

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