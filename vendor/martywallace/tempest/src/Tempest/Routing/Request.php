<?php

namespace Tempest\Routing;

use Exception;


/**
 * Holds information representing a client request to the application.
 *
 * @property-read string $uri The requested URI.
 * @property-read string $method The request method e.g. GET, POST.
 *
 * @package Tempest\Routing
 * @author Marty Wallace.
 */
class Request
{

    const METHOD_GET = 'get';
    const METHOD_POST = 'post';


    /** @var string */
    private $_uri;

    /** @var string */
    private $_method;

    /** @var array[] */
    private $_data = array();


    public function __construct()
    {
        $this->_uri = $_SERVER['PATH_INFO'];
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);

        $this->_data = array(
            'get' => $_GET,
            'post' => $_POST
        );
    }


    public function __get($prop)
    {
        if ($prop === 'uri') return $this->_uri;
        if ($prop === 'method') return $this->_method;

        return $this->data($prop);
    }


    public function data($name = null, $fallback = null)
    {
        $source = $this->_data[$this->_method];

        if ($name !== null)
        {
            if (array_key_exists($name, $source))
            {
                return $source[$name];
            }
            else
            {
                // Search other request data blocks as a first fallback.
                foreach ($this->_data as $method => $data)
                {
                    if (array_key_exists($name, $data)) return $data[$name];
                }
            }

            return $fallback;
        }

        return $source;
    }

}