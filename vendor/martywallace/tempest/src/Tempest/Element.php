<?php

namespace Tempest;

use Exception;


/**
 * An application element, used as a container for zero or more components.
 *
 * @property-read Component[] $components A list of Components attached to this Element.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Element
{

    /** @var Component[] */
    private $_components = array();


    public function __get($prop)
    {
        if ($prop === 'components') return $this->_components;

        if (array_key_exists($prop, $this->_components))
        {
            // We found a component with a matching name.
            return $this->_components[$prop];
        }

        return null;
    }


    /**
     * Add a Component to this Element.
     * @param string $name The name used to reference the Component.
     * @param Component $component The Component to add.
     * @return Component|null
     * @throws Exception
     */
    public function addComponent($name, Component $component)
    {
       if (!$this->hasComponent($name))
       {
           $this->_components[$name] = $component;
           return $component;
       }
       else
       {
           throw new Exception('A Component named "' . $name . '" already exists on this Element.');
       }
    }


    /**
     * Determine whether or not a Component with the specified name exists on this Element.
     * @param string $name The name to check.
     * @return bool
     */
    public function hasComponent($name)
    {
        return array_key_exists($name, $this->_components);
    }

}