<?php
namespace Chamilo\Libraries\Architecture\Application;

class Parameters
{

    /**
     *
     * @var multitype:string
     */
    private $parameters;

    private static $instance;

    /**
     * Returns the current URL parameters.
     * 
     * @return multitype:string
     */
    public function get_parameters(Application $application)
    {
        $application_hashes = array();
        $application_hashes[] = spl_object_hash($application);
        
        while ($application->get_application() instanceof Application)
        {
            $application_hashes[] = spl_object_hash($application->get_application());
            $application = $application->get_application();
        }
        
        $application_hashes = array_reverse($application_hashes);
        
        $heap = array();
        $parameters = &$this->parameters;
        
        foreach ($application_hashes as $application_hash)
        {
            if (isset($parameters[$application_hash]) && isset($parameters[$application_hash]['parameters']))
            {
                foreach ($parameters[$application_hash]['parameters'] as $key => $value)
                {
                    $heap[$key] = $value;
                }
            }
            $parameters = &$parameters[$application_hash];
        }
        
        return $heap;
    }

    /**
     * Returns the value of the given URL parameter.
     * 
     * @param string $name
     * @return string
     */
    public function get_parameter(Application $application, $name)
    {
        $parameters = &$this->determine_level($application);
        
        if (array_key_exists($name, $parameters))
        {
            return $parameters[$name];
        }
    }

    /**
     * Sets the value of a URL parameter.
     * 
     * @param string $name
     * @param string $value
     */
    public function set_parameter(Application $application, $name, $value)
    {
        $parameters = &$this->determine_level($application);
        $parameters[$name] = $value;
    }

    private function &determine_level(Application $application)
    {
        $application_hashes = array();
        $application_hashes[] = spl_object_hash($application);
        
        while ($application->get_application() instanceof Application)
        {
            $application_hashes[] = spl_object_hash($application->get_application());
            $application = $application->get_application();
        }
        
        $application_hashes = array_reverse($application_hashes);
        
        $parameters = &$this->parameters;
        
        foreach ($application_hashes as $application_hash)
        {
            if (! isset($parameters[$application_hash]) || ! isset($parameters[$application_hash]['parameters']))
            {
                
                $parameters[$application_hash] = array();
                $parameters[$application_hash]['parameters'] = array();
            }
            $parameters = &$parameters[$application_hash];
        }
        
        return $parameters['parameters'];
    }

    /**
     *
     * @param multitype:string $parameters
     */
    private function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @return Parameters
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
}