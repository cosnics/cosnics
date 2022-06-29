<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @var string[]
     */
    private $postDataValues = [];

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->validateRequest();
    }

    /**
     * Returns the value of the given parameter.
     *
     * @param string $name
     *
     * @return string
     */
    public function getPostDataValue($name)
    {
        if (array_key_exists($name, $this->postDataValues))
        {
            return $this->postDataValues[$name];
        }
    }

    /**
     * Get the postDataValues
     *
     * @return string[]
     */
    public function getPostDataValues()
    {
        return $this->postDataValues;
    }

    /**
     * Set the postDataValues
     *
     * @param string[] $postDataValues
     */
    public function setPostDataValues($postDataValues)
    {
        $this->postDataValues = $postDataValues;
    }

    /**
     *
     * @param string $parameter
     *
     * @return string
     */
    public function getRequestedPostDataValue($parameter)
    {
        $getValue = $this->getRequest()->query->get($parameter);

        if (!isset($getValue))
        {
            $postValue = $this->getRequest()->request->get($parameter);

            if (!isset($postValue))
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
     * @return string[]
     */
    public function getRequiredPostParameters(): array
    {
        return [];
    }

    /**
     *
     * @param $exception
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function handleException($exception)
    {
        $this->getExceptionLogger()->logException($exception);

        return new JsonResponse(null, 500);
    }

    /**
     * Sets the value of a parameter.
     *
     * @param string $name
     * @param string $value
     */
    public function setPostDataValue($name, $value)
    {
        $this->postDataValues[$name] = $value;
    }

    /**
     * Validate the AJAX call, if not validated, trigger an HTTP 400 (Bad request) error
     */
    public function validateRequest()
    {
        foreach ($this->getRequiredPostParameters() as $parameter)
        {
            $value = $this->getRequestedPostDataValue($parameter);
            if (!is_null($value))
            {
                $this->setPostDataValue($parameter, $value);
            }
            else
            {
                JsonAjaxResult::bad_request('Invalid Post parameters');
            }
        }
    }
}
