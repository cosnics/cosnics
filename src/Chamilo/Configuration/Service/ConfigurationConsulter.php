<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationConsulter extends DataConsulter
{

    /**
     *
     * @return string[]
     */
    public function getSettings()
    {
        return $this->getData();
    }

    /**
     * Gets a parameter from the configuration.
     * 
     * @param string[] $keys
     * @throws \Exception
     * @return string
     */
    /**
     *
     * @param string[] $keys
     * @return string
     */
    public function getSetting($keys)
    {
        $variables = $keys;
        $values = $this->getSettings();
        
        while (count($variables) > 0)
        {
            $key = array_shift($variables);
            
            if (! array_key_exists($key, $values))
            {
                throw new \Exception(
                    'The requested variable is not available in an unconfigured environment (' . implode(' > ', $keys) .
                         ')');
            }
            else
            {
                $values = $values[$key];
            }
        }
        
        return $values;
    }

    /**
     *
     * @param string[] $keys
     * @param string $value
     */
    protected function setSetting($keys, $value)
    {
        $variables = $keys;
        $values = &$this->getSettings();
        
        while (count($variables) > 0)
        {
            $key = array_shift($variables);
            
            if (! isset($values[$key]))
            {
                $values[$key] = null;
                $values = &$values[$key];
            }
            else
            {
                $values = &$values[$key];
            }
        }
        
        $values = $value;
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function hasSettingsForContext($context)
    {
        $settings = $this->getSettings();
        return isset($settings[$context]);
    }
}
