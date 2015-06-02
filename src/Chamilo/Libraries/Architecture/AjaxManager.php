<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AjaxManager extends Application
{

    /**
     * An array of parameters as passed by the POST-request
     *
     * @var array
     */
    private $postDataValues = array();

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent :: __construct($applicationConfiguration);
        $this->validateRequest();
    }

    /**
     * Validate the AJAX call, if not validated, trigger an HTTP 400 (Bad request) error
     */
    public function validateRequest()
    {
        foreach ($this->getRequiredPostParameters() as $parameter)
        {
            $value = $this->getRequestedPostDataValue($parameter);
            if (! is_null($value))
            {
                $this->setPostDataValue($parameter, $value);
            }
            else
            {
                JsonAjaxResult :: bad_request('Invalid Post parameters');
            }
        }
    }

    /**
     *
     * @param string $parameter
     * @return string
     */
    public function getRequestedPostDataValue($parameter)
    {
        $getValue = Request :: get($parameter);

        if (! $getValue)
        {
            $postValue = Request :: post($parameter);

            if (! $postValue)
            {
                return null;
            }
            else
            {
                return $postValue;
            }
        }
        else
        {
            return $getValue;
        }
    }

    /**
     * Get the postDataValues
     *
     * @return array
     */
    public function getPostDataValues()
    {
        return $this->postDataValues;
    }

    /**
     * Set the postDataValues
     *
     * @param array $postDataValues
     */
    public function setPostDataValues($postDataValues)
    {
        $this->postDataValues = $postDataValues;
    }

    /**
     * Returns the value of the given parameter.
     *
     * @param string $name The parameter name.
     * @return string The parameter value.
     */
    public function getPostDataValue($name)
    {
        if (array_key_exists($name, $this->postDataValues))
        {
            return $this->postDataValues[$name];
        }
    }

    /**
     * Sets the value of a parameter.
     *
     * @param string $name The parameter name.
     * @param string $value The parameter value.
     */
    public function setPostDataValue($name, $value)
    {
        $this->postDataValues[$name] = $value;
    }

    /**
     * Get an array of parameters which should be set for this call to work
     *
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array();
    }
}
