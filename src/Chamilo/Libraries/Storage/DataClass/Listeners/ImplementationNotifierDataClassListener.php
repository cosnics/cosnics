<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Dataclass listener which manipulates the crud methods to notify the implementation packages
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImplementationNotifierDataClassListener extends DataClassListener
{

    /**
     * **************************************************************************************************************
     * Properties *
     * *************************************************************************************************************
     */
    
    /**
     * The DataClass (must implement the necessary interface)
     * 
     * @var DataClass
     */
    private $data_class;

    /**
     * The context for which the implementation packages must be searched
     * 
     * @var
     *
     *
     *
     *
     *
     *
     *
     *
     */
    private $context;

    /**
     * The mapping between the methods of the data class listener and the methods of the datamanager, at least one
     * method mapping is required
     * 
     * @var array
     */
    private $method_mapping;

    /**
     * Cache for the implementation packages
     * 
     * @var array
     */
    private $implementation_packages;

    /**
     * Constructs this dataclass listener and checks if the dataclass implements the necessary functions
     * 
     * @param DataClass $data_class
     * @param $context
     * @param array $method_mapping
     *
     * @throws \Exception
     */
    public function __construct(DataClass $data_class, $context, array $method_mapping = array())
    {
        $this->set_data_class($data_class);
        $this->set_context($context);
        $this->set_method_mapping($method_mapping);
    }

    /**
     * Sets the context
     * 
     * @param mixed $context
     *
     * @throws \InvalidArgumentException
     */
    public function set_context($context)
    {
        if (empty($context))
        {
            throw new \InvalidArgumentException('The context should not be empty');
        }
        $this->context = $context;
    }

    /**
     * Sets the data class
     * 
     * @param \libraries\storage\DataClass $data_class
     *
     * @throws \InvalidArgumentException
     */
    public function set_data_class($data_class)
    {
        if (! $data_class instanceof DataClass)
        {
            throw new \InvalidArgumentException('The data class should be an instance of DataClass');
        }
        $this->data_class = $data_class;
    }

    /**
     * Sets the method mapping
     * 
     * @param array $method_mapping
     *
     * @throws \Exception
     */
    public function set_method_mapping($method_mapping)
    {
        if (! is_array($method_mapping) || count($method_mapping) == 0)
        {
            throw new \InvalidArgumentException('The method mapping should at least contain 1 method');
        }
        
        foreach ($method_mapping as $method => $data_manager_method)
        {
            if (! method_exists($this, $method))
            {
                throw new \InvalidArgumentException(
                    'The method ' . $method . ' does not exist in the data class listener');
            }
        }
        
        $this->method_mapping = $method_mapping;
    }

    /**
     * Notifies the implementation packages for the given data class listener method
     * 
     * @param string $data_class_listener_method
     * @param array $parameters
     *
     * @return bool
     */
    protected function notify_implementation_packages($data_class_listener_method, array $parameters = array())
    {
        if (! array_key_exists($data_class_listener_method, $this->method_mapping))
        {
            return true;
        }
        
        array_unshift($parameters, $this->data_class);
        
        $method = $this->method_mapping[$data_class_listener_method];
        
        $packages = $this->get_implementation_packages();
        
        foreach ($packages as $package)
        {
            $class_name = $package . '\DataManager';
            
            if (! method_exists($class_name, $method))
            {
                continue;
            }
            
            if (! call_user_func_array(array($class_name, $method), $parameters))
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Determines the implementation packages based on the given context
     * 
     * @return array
     */
    protected function get_implementation_packages()
    {
        if (! isset($this->implementation_packages))
        {
            $pattern = '*\\\Integration\\' . $this->context;
            
            $condition = new PatternMatchCondition(
                new PropertyConditionVariable(Registration::class_name(), Registration::PROPERTY_CONTEXT), 
                $pattern);
            
            $packages = array();
            
            $package_registrations = DataManager::retrieves(
                Registration::class_name(), 
                new DataClassRetrievesParameters($condition));
            while ($package_registration = $package_registrations->next_result())
            {
                $packages[] = $package_registration->get_context();
            }
            
            $this->implementation_packages = $packages;
        }
        
        return $this->implementation_packages;
    }

    /**
     * Calls this function before the creation of a dataclass in the database
     * 
     * @return bool
     */
    public function on_before_create()
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function after the creation of a dataclass in the database
     * 
     * @param bool $success
     *
     * @return bool
     */
    public function on_after_create($success)
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function before the update of a dataclass in the database
     */
    public function on_before_update()
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function after the update of a dataclass in the database
     * 
     * @param bool $success
     *
     * @return bool
     */
    public function on_after_update($success)
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function before the deletion of a dataclass in the database
     * 
     * @return bool
     */
    public function on_before_delete()
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function after the deletion of a dataclass in the database
     * 
     * @param bool $success
     *
     * @return bool
     */
    public function on_after_delete($success)
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function before a property is set
     * 
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function on_before_set_property($name, $value)
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function after a property is set
     * 
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function on_after_set_property($name, $value)
    {
        return $this->notify_implementation_packages(__FUNCTION__, func_get_args());
    }

    /**
     * Calls this function to return the dependencies of this class
     * 
     * @param array $dependencies
     *
     * @return bool
     */
    public function on_get_dependencies(&$dependencies = array())
    {
        return $this->notify_implementation_packages(__FUNCTION__, array(&$dependencies));
    }
}