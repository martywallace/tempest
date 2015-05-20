<?php

namespace Tempest\Rendering;

use Tempest\Component;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Syntax;
use Twig_Error_Runtime;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Exception;


/**
 * Wraps the Twig framework for delivering templates.
 *
 * @property-read Twig_Environment $environment The internal Twig_Environment instance.
 * @property-read Twig_Loader_Filesystem $loader The internal Twig_Loader_Filesystem instance.
 * @property-read TwigExtensions $extensions The internal TwigExtensions class, defining Tempest level extensions.
 *
 * @package Tempest\Rendering
 * @author Marty Wallace
 */
class TwigComponent extends Component
{

    const TEMPEST_NAMESPACE = 'tempest';


    /** @var Twig_Loader_Filesystem */
    private $_loader;

    /** @var Twig_Environment */
    private $_environment;

    /** @var TwigExtensions */
    private $_extensions;


    public function __construct()
    {
        $this->_loader = new Twig_Loader_Filesystem();

        $this->addTemplatePath('vendor/martywallace/tempest/templates', self::TEMPEST_NAMESPACE);
        $this->addTemplatePath(app()->config('templates', array()));

        $this->_environment = new Twig_Environment($this->_loader, array(
            'debug' => app()->config('dev')
        ));

        $this->_extensions = new TwigExtensions($this);
    }


    public function __get($prop)
    {
        if ($prop === 'environment') return $this->_environment;
        if ($prop === 'loader') return $this->_loader;
        if ($prop === 'extensions') return $this->_extensions;

        return null;
    }


    /**
     * Render a Twig template.
     * @param string $template The template to render, relative to a template path assigned.
     * @param array $data Data to bind to the template.
     * @return string
     * @throws Twig_Error_Loader When the template cannot be found.
     * @throws Twig_Error_Syntax When an error occurred during compilation.
     * @throws Twig_Error_Runtime When an error occurred during rendering.
     */
    public function render($template, Array $data = null)
    {
        $data = $data === null ? array() : $data;

        $data = array_merge($data, array(
            'app' => app()
        ));

        return $this->_environment->render($template, $data);
    }


    /**
     * Add an extension to Twig.
     * @param string $type The extension type, see TwigExtension::TYPE_FILTER and TwigExtension::TYPE_FUNCTION.
     * @param string $handle The handle associated with the extension.
     * @param callable $callable The callable to execute when triggering the extension.
     * @throws Exception If the extension type requested is unknown.
     */
    public function extend($type, $handle, $callable)
    {
        if ($type === TwigExtensions::TYPE_FILTER || $type === TwigExtensions::TYPE_FUNCTION)
        {
            // Lol PHP's case insensitivity.
            // $type = ucfirst($type);

            $class = 'Twig_Simple' . $type;

            $this->_environment->{'add' . $type}(
                new $class($handle, $callable)
            );
        }

        else throw new Exception('Unknown extension type "' . $type . '".');
    }


    /**
     * Adds a new location relative to the application root where templates can be searched for.
     * @param string|array $path A path or array of paths to search for templates.
     * @param string $namespace An optional namespace to use for templates.
     * @throws Twig_Error_Loader
     */
    public function addTemplatePath($path, $namespace = Twig_Loader_Filesystem::MAIN_NAMESPACE)
    {
        if (is_array($path))
        {
            foreach($path as $p)
            {
                // Recursive path additions.
                $this->addTemplatePath($p, $namespace);
            }
        }
        else
        {
            $path = trim($path, '/');
            $this->_loader->prependPath(ROOT . '/' . $path, $namespace);
        }
    }

}