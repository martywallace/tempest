# Tempest

Tempest is a tiny PHP framework for developing small to medium sized websites and applications. It is a lightweight alternative to heavier frameworks like [Laravel](http://laravel.com/); for developers who only need basic routing and general project structure to get going.

## What's happening?

I am currently making major revisions to the framework for a 1.0.0 release. It will follow a modern project structure and drop its existing templating system for [Twig](http://twig.sensiolabs.org/).

## Prerequisites

Tempest makes use of various tools to streamline the development process:

* [Composer](https://getcomposer.org/), for managing PHP dependencies.
* [NodeJS & Node Package Manager](http://nodejs.org/), for managing Grunt tasks.
* [Grunt](http://gruntjs.com/), for running Grunt tasks.

## Getting Started

1. Download and extract the files to your server - `public` should be set as the server root.
	* For XAMPP users - refer to [these instructions](http://sawmac.com/xampp/virtualhosts/) for setting up a virtual host to run Tempest.
	* Using PHP's [inbuilt server](http://php.net/manual/en/features.commandline.webserver.php) is much easier than the above.
2. Tempest has some dependencies that are managed via [composer](https://getcomposer.org). You will need to run `composer install` once the files are extracted.
3. Tempest also uses Grunt to compile SASS & JavaScript files into the public directory; you will need to run `npm install` to download the Grunt tasks and then run `grunt` to compile those files.
4. Good to go.

## Releases

* [0.0.1](https://github.com/MartyWallace/Tempest/releases/tag/0.0.1)
	* This release is explained in the [wiki section](https://github.com/MartyWallace/Tempest/wiki).
    * Custom logicless templating.
    * Easy to set up (no need for Composer, Grunt, virtual host setup, etc.
    * Runs correctly in subdirectories as its own application.

## License

Copyright 2015 Marty Wallace. This is a free & open source project licensed under [MIT](http://opensource.org/licenses/MIT).