<?php
namespace Chamilo\Libraries\Architecture\Application;

/**
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Parameters
{

    private static ?Parameters $instance = null;

    private array $parameters = [];

    public static function getInstance(): Parameters
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function get_parameter(Application $application, string $name)
    {
        $applicationHash = spl_object_hash($application);

        if (array_key_exists($applicationHash, $this->parameters))
        {
            return $this->parameters[$applicationHash][$name];
        }

        return null;
    }

    public function get_parameters(Application $application): array
    {
        $applicationHashes = [];
        $applicationHashes[] = spl_object_hash($application);

        while ($application->get_application() instanceof Application)
        {
            $applicationHashes[] = spl_object_hash($application->get_application());
            $application = $application->get_application();
        }

        $applicationHashes = array_reverse($applicationHashes);

        $aggregatedParameters = [];

        foreach ($applicationHashes as $applicationHash)
        {
            if (array_key_exists($applicationHash, $this->parameters))
            {
                foreach ($this->parameters[$applicationHash] as $key => $value)
                {
                    $aggregatedParameters[$key] = $value;
                }
            }
        }

        return $aggregatedParameters;
    }

    public function set_parameter(Application $application, string $name, $value = null)
    {
        $applicationHash = spl_object_hash($application);

        if (!array_key_exists($applicationHash, $this->parameters))
        {
            $this->parameters[$applicationHash] = [];
        }

        $this->parameters[$applicationHash][$name] = $value;
    }
}