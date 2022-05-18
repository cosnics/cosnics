<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Core;

use InvalidArgumentException;

/**
 * This class represents a dependency container which can be used to store dependencies of a class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyContainer
{

    /**
     * The list of dependencies
     * 
     * @var mixed[]
     */
    private $dependencies;

    /**
     * Constructor
     * 
     * @param mixed[] $dependencies
     */
    public function __construct($dependencies = [])
    {
        $this->set_dependencies($dependencies);
    }

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds a new dependency to the list of dependencies
     * 
     * @param string $dependency_name
     * @param mixed $dependency
     */
    public function add($dependency_name, $dependency)
    {
        if (empty($dependency_name))
        {
            throw new InvalidArgumentException('Dependency name can not be empty');
        }
        
        $this->check_valid_dependency($dependency);
        
        if (array_key_exists($dependency_name, $this->dependencies))
        {
            throw new InvalidArgumentException(
                'Dependency name must not exist, if you want to overwrite an existing' .
                     'dependency use the replace function');
        }
        
        $this->dependencies[$dependency_name] = $dependency;
    }

    /**
     * Deletes a dependency from the list of dependencies
     * 
     * @param string $dependency_name
     */
    public function delete($dependency_name)
    {
        if (empty($dependency_name))
        {
            throw new InvalidArgumentException('Dependency name can not be empty');
        }
        
        if (! array_key_exists($dependency_name, $this->dependencies))
        {
            throw new InvalidArgumentException('Dependency name must exist in the container');
        }
        
        unset($this->dependencies[$dependency_name]);
    }

    /**
     * Replaces an existing dependency with a new dependency
     * 
     * @param string $dependency_name
     * @param mixed $dependency
     */
    public function replace($dependency_name, $dependency)
    {
        if (empty($dependency_name))
        {
            throw new InvalidArgumentException('Dependency name can not be empty');
        }
        
        if (! array_key_exists($dependency_name, $this->dependencies))
        {
            throw new InvalidArgumentException(
                'Dependency name must exist in the container,
                if you want to add a new dependency use the add function');
        }
        
        $this->check_valid_dependency($dependency);
        
        $this->dependencies[$dependency_name] = $dependency;
    }

    /**
     * Returns a dependency with a given dependency name
     * 
     * @param string $dependency_name
     */
    public function get($dependency_name)
    {
        if (empty($dependency_name))
        {
            throw new InvalidArgumentException('Dependency name can not be empty');
        }
        
        if (! array_key_exists($dependency_name, $this->dependencies))
        {
            throw new InvalidArgumentException('Dependency name must exist in the container');
        }
        
        return $this->dependencies[$dependency_name];
    }

    /**
     * Checks if a dependency is valid, meaning not null and either and object or a class name.
     * 
     * @param mixed $dependency
     */
    private function check_valid_dependency($dependency)
    {
        if (is_null($dependency) || (! is_object($dependency) && ! class_exists($dependency)))
        {
            throw new InvalidArgumentException('Dependency must be an existing object or class name');
        }
    }
    
    /**
     * Returns the dependencies
     * 
     * @return mixed[] $dependencies
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Sets the dependencies
     * 
     * @param mixed[] $dependencies
     */
    public function set_dependencies($dependencies = [])
    {
        $this->dependencies = $dependencies;
    }
}
