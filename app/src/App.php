<?php

use Tempest\Tempest;


/**
 * Your application.
 */
class App extends Tempest {

    /**
     * Set up the application.
     */
    protected function setup() {
        echo app()->twig->render('index.html');
    }

}