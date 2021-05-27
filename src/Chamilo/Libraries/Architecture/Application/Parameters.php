<?php
namespace Chamilo\Libraries\Architecture\Application;

/**
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Parameters
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Parameters
     */
    private static $instance;

    /**
     *
     * @var string[]
     */
    private $parameters;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     *
     * @return string
     */
    private function &determine_level(Application $application)
    {
        $application_hashes = [];
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
            if (!isset($parameters[$application_hash]) || !isset($parameters[$application_hash]['parameters']))
            {

                $parameters[$application_hash] = [];
                $parameters[$application_hash]['parameters'] = [];
            }
            $parameters = &$parameters[$application_hash];
        }

        return $parameters['parameters'];
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Parameters
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Returns the value of the given URL parameter.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string $name
     *
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
     * Returns the current URL parameters.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     *
     * @return string[]
     */
    public function get_parameters(Application $application)
    {
        $application_hashes = [];
        $application_hashes[] = spl_object_hash($application);

        while ($application->get_application() instanceof Application)
        {
            $application_hashes[] = spl_object_hash($application->get_application());
            $application = $application->get_application();
        }

        $application_hashes = array_reverse($application_hashes);

        $heap = [];
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
     * Sets the value of a URL parameter.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string $name
     * @param string $value
     */
    public function set_parameter(Application $application, $name, $value)
    {
        $parameters = &$this->determine_level($application);
        $parameters[$name] = $value;
    }

    /**
     *
     * @param string[] $parameters
     */
    private function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}