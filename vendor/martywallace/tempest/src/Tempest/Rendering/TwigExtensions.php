<?php

namespace Tempest\Rendering;


/**
 * This class attaches Tempest level extensions to Twig.
 * @package Tempest\Rendering
 * @author Marty Wallace
 */
class TwigExtensions
{

    const TYPE_FILTER = 'filter';
    const TYPE_FUNCTION = 'function';


    /**
     * @param TwigComponent $twig
     */
    public function __construct(TwigComponent $twig)
    {
        $twig->extend(self::TYPE_FUNCTION, 'link', array($this, 'link'));
    }


    /**
     * Creates an absolute link relative to the "url" configuration value.
     * @param string $value The link relative to the public site URL.
     * @return string
     */
    public function link($value)
    {
        $baseUrl = rtrim(app()->config('url', ''), '/') . '/';
        return $baseUrl . ltrim($value, '/');
    }

}