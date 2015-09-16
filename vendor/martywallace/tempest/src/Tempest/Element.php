<?php

namespace Tempest;

use Tempest\Components\Component;
use Exception;


/**
 * An application element, used as a container for zero or more components.
 *
 * @property-read Component[] $components A list of Components attached to this Element.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Element {




    public function __get($prop) {
        if ($prop === 'components') return $this->_components;



        return null;
    }


    public function __isset($prop) {
        // Mostly here to satisfy Twig, see:
        // https://github.com/twigphp/Twig/issues/360
        return $this->{$prop} !== null;
    }


    public function __set($prop, $value) {
        // Help a brother out and avoid annoying bugs via typos.
        throw new Exception('Property "' . $prop . '" does not exist on "' . get_class($this) . '" and cannot be dynamically created.');
    }




}