# Tempest

[![Build Status](https://travis-ci.org/martywallace/tempest.svg?branch=master)](https://travis-ci.org/martywallace/tempest)
[![Latest Stable Version](https://poser.pugx.org/martywallace/tempest/v/stable)](https://packagist.org/packages/martywallace/tempest)
[![Total Downloads](https://poser.pugx.org/martywallace/tempest/downloads)](https://packagist.org/packages/martywallace/tempest)

A PHP framework with a strong focus on IDE support through correct PHPDoc usage
and maintenance. Other core features include:

* Straightforward bootstrapping with zero default project structure, allowing you to use the framework however you prefer.
* Environment based configuration.
* A simple, elegant system for provisioning and calling application services, where the bulk of your application code will be written and called from.
* Familiar HTTP request + response routing and lifecycle with middleware support.

## Installation

Tempest can be [found on Packagist](https://packagist.org/packages/martywallace/tempest) and installed with Composer:

	$ composer require martywallace/tempest

However for new projects it is recommended to use the [`tempest-app`](https://github.com/martywallace/tempest-app)
boilerplate project with Composer's `create-project`. This will scaffold a new project using the ideal structure for
building an application with Tempest:

    $ composer create-project martywallace/tempest-app my-app
