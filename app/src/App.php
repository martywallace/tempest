<?php

use Tempest\Tempest;


/**
 * Your application.
 */
class App extends Tempest
{

    /**
     * Set up the application.
     */
    protected function setup()
    {
        // Application level templates.
        app()->twig->addTemplatePath(app()->config('templates'));

        echo app()->twig->render('index.html');
    }

}