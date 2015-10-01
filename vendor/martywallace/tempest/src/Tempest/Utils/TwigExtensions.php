<?php namespace Tempest\Utils;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;


/**
 * This class attaches Tempest level extensions to Twig.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
class TwigExtensions extends Twig_Extension {

    public function getName() { return 'TempestTwigExtensions'; }

    public function getGlobals() {
        return [
            // Bind the application to Twig templates.
            'app' => app()
        ];
    }

    public function getFilters() {
        return [
            new Twig_SimpleFilter('hash', 'sha1')
        ];
    }

    public function getFunctions() {
        return [
            new Twig_SimpleFunction('link', [$this, 'link'])
        ];
    }


    /**
     * Creates an absolute link relative to the "url" configuration value.
     *
     * @param string $value The link relative to the public site URL.
     *
     * @return string
     */
    public function link($value) {
        return app()->url . '/' . ltrim($value, '/');
    }

}