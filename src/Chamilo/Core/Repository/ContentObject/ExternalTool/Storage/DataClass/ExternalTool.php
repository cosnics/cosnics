<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass;

use Chamilo\Application\Lti\Domain\Provider\ProviderInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalTool extends ContentObject implements ProviderInterface
{
    const PROPERTY_LTI_PROVIDER_ID = 'lti_provider_id';
    const PROPERTY_UUID = 'uuid';
    const PROPERTY_LAUNCH_URL = 'lti_url';
    const PROPERTY_KEY = 'consumer_key';
    const PROPERTY_SECRET = 'consumer_secret';
    const PROPERTY_CUSTOM_PARAMETERS = 'custom_parameters';

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_LTI_PROVIDER_ID, self::PROPERTY_UUID, self::PROPERTY_LAUNCH_URL, self::PROPERTY_KEY,
            self::PROPERTY_SECRET, self::PROPERTY_CUSTOM_PARAMETERS
        );
    }

    /**
     * @return string
     */
    public function getLtiProviderId()
    {
        return $this->get_additional_property(self::PROPERTY_LTI_PROVIDER_ID);
    }

    /**
     * @param int $ltiProviderId
     *
     * @return $this
     */
    public function setLtiProviderId(int $ltiProviderId = null)
    {
        $this->set_additional_property(self::PROPERTY_LTI_PROVIDER_ID, $ltiProviderId);
        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->get_additional_property(self::PROPERTY_UUID);
    }

    /**
     * @param string $uuid
     *
     * @return $this
     */
    public function setUUID(string $uuid)
    {
        $this->set_additional_property(self::PROPERTY_UUID, $uuid);
        return $this;
    }

    /**
     * @return string
     */
    public function getLaunchUrl()
    {
        return $this->get_additional_property(self::PROPERTY_LAUNCH_URL);
    }

    /**
     * @param string $launchUrl
     *
     * @return $this
     */
    public function setLaunchUrl(string $launchUrl = null)
    {
        $this->set_additional_property(self::PROPERTY_LAUNCH_URL, $launchUrl);
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->get_additional_property(self::PROPERTY_KEY);
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key = null)
    {
        $this->set_additional_property(self::PROPERTY_KEY, $key);
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->get_additional_property(self::PROPERTY_SECRET);
    }

    /**
     * @param string $secret
     *
     * @return $this
     */
    public function setSecret(string $secret = null)
    {
        $this->set_additional_property(self::PROPERTY_SECRET, $secret);
        return $this;
    }

    /**
     * @return ExternalToolCustomParameter[]
     */
    public function getCustomParameters()
    {
        $customParameterObjects = [];

        $customParameters = json_decode($this->get_additional_property(self::PROPERTY_CUSTOM_PARAMETERS), true);
        foreach ($customParameters as $customParameter)
        {
            $customParameterObjects[] = ExternalToolCustomParameter::fromArray($customParameter);

        }

        return $customParameterObjects;
    }

    /**
     * @param ExternalToolCustomParameter[] $customParameters
     */
    public function setCustomParameters(array $customParameters)
    {
        $customParametersArray = [];

        foreach($customParameters as $customParameter)
        {
            $customParametersArray[] = $customParameter->toArray();
        }

        $this->set_additional_property(self::PROPERTY_CUSTOM_PARAMETERS, json_encode($customParametersArray));
    }

    /**
     * @return string
     */
    public function getCustomParametersJSON()
    {
        $json = $this->get_additional_property(self::PROPERTY_CUSTOM_PARAMETERS);
        if(empty($json))
        {
            return json_encode([]);
        }

        return $json;
    }

    /**
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->getUuid();
    }

    /**
     * @return bool
     */
    public function isValidCustomProvider()
    {
        return !empty($this->getLaunchUrl());
    }
}
