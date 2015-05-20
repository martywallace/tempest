<?php

namespace Tempest\Routing;

use Exception;
use Tempest\Component;


/**
 * The router is responsible for taking a request and translating it to a suitable response.
 * @package Tempest\Routing
 * @author Marty Wallace
 */
class Router extends Component
{

    /** @var Request */
    private $_request;


    public function __construct()
    {
        $this->_request = new Request();
    }


    public function __get($prop)
    {
        if ($prop === 'request') return $this->_request;

        return null;
    }

}