<?php
namespace Chamilo\Application\CasStorage\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Application\CasStorage\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Service extends DataClass
{
    const PROPERTY_EXPRESSION_TYPE = 'expression_type';
    const PROPERTY_ALLOWED_TO_PROXY = 'allowedToProxy';
    const PROPERTY_ANONYMOUS_ACCESS = 'anonymousAccess';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ENABLED = 'enabled';
    const PROPERTY_EVALUATION_ORDER = 'evaluation_order';
    const PROPERTY_IGNORE_ATTRIBUTES = 'ignoreAttributes';
    const PROPERTY_NAME = 'name';
    const PROPERTY_REQUIRED_HANDLERS = 'required_handlers';
    const PROPERTY_SERVICE_ID = 'serviceId';
    const PROPERTY_SSO_ENABLED = 'ssoEnabled';
    const PROPERTY_THEME = 'theme';
    const PROPERTY_USERNAME_ATTRIBUTE = 'username_attr';

    // Status
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_EXPRESSION_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ALLOWED_TO_PROXY;
        $extendedPropertyNames[] = self::PROPERTY_ANONYMOUS_ACCESS;
        $extendedPropertyNames[] = self::PROPERTY_DESCRIPTION;
        $extendedPropertyNames[] = self::PROPERTY_ENABLED;
        $extendedPropertyNames[] = self::PROPERTY_EVALUATION_ORDER;
        $extendedPropertyNames[] = self::PROPERTY_IGNORE_ATTRIBUTES;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_REQUIRED_HANDLERS;
        $extendedPropertyNames[] = self::PROPERTY_SERVICE_ID;
        $extendedPropertyNames[] = self::PROPERTY_SSO_ENABLED;
        $extendedPropertyNames[] = self::PROPERTY_THEME;
        $extendedPropertyNames[] = self::PROPERTY_USERNAME_ATTRIBUTE;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    public function getExpressionType()
    {
        return $this->get_default_property(self::PROPERTY_EXPRESSION_TYPE);
    }

    public function setExpressionType($expressionType)
    {
        $this->set_default_property(self::PROPERTY_EXPRESSION_TYPE, $expressionType);
        return $this;
    }

    public function getAllowedToProxy()
    {
        return $this->get_default_property(self::PROPERTY_ALLOWED_TO_PROXY);
    }

    public function setAllowedToProxy($allowedToProxy)
    {
        $this->set_default_property(self::PROPERTY_ALLOWED_TO_PROXY, $allowedToProxy);
        return $this;
    }

    public function getAnonymousAccess()
    {
        return $this->get_default_property(self::PROPERTY_ANONYMOUS_ACCESS);
    }

    public function setAnonymousAccess($anonymousAccess)
    {
        $this->set_default_property(self::PROPERTY_ANONYMOUS_ACCESS, $anonymousAccess);
        return $this;
    }

    public function getDescription()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
        return $this;
    }

    public function getEnabled()
    {
        return $this->get_default_property(self::PROPERTY_ENABLED);
    }

    public function setEnabled($enabled)
    {
        $this->set_default_property(self::PROPERTY_ENABLED, $enabled);
        return $this;
    }

    public function getEvaluationOrder()
    {
        return $this->get_default_property(self::PROPERTY_EVALUATION_ORDER);
    }

    public function setEvaluationOrder($evaluationOrder)
    {
        $this->set_default_property(self::PROPERTY_EVALUATION_ORDER, $evaluationOrder);
        return $this;
    }

    public function getIgnoreAttributes()
    {
        return $this->get_default_property(self::PROPERTY_IGNORE_ATTRIBUTES);
    }

    public function setIgnoreAttributes($ignoreAttributes)
    {
        $this->set_default_property(self::PROPERTY_IGNORE_ATTRIBUTES, $ignoreAttributes);
        return $this;
    }

    public function getName()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    public function setName($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
        return $this;
    }

    public function getRequiredHandlers()
    {
        return $this->get_default_property(self::PROPERTY_REQUIRED_HANDLERS);
    }

    public function setRequiredHandlers($requiredHandlers)
    {
        $this->set_default_property(self::PROPERTY_REQUIRED_HANDLERS, $requiredHandlers);
        return $this;
    }

    public function getServiceId()
    {
        return $this->get_default_property(self::PROPERTY_SERVICE_ID);
    }

    public function setServiceId($serviceId)
    {
        $this->set_default_property(self::PROPERTY_SERVICE_ID, $serviceId);
        return $this;
    }

    public function getSsoEnabled()
    {
        return $this->get_default_property(self::PROPERTY_SSO_ENABLED);
    }

    public function setSsoEnabled($ssoEnabled)
    {
        $this->set_default_property(self::PROPERTY_SSO_ENABLED, $ssoEnabled);
        return $this;
    }

    public function getTheme()
    {
        return $this->get_default_property(self::PROPERTY_THEME);
    }

    public function setTheme($theme)
    {
        $this->set_default_property(self::PROPERTY_THEME, $theme);
        return $this;
    }

    public function getUsernameAttribute()
    {
        return $this->get_default_property(self::PROPERTY_USERNAME_ATTRIBUTE);
    }

    public function setUsernameAttribute($usernameAttribute)
    {
        $this->set_default_property(self::PROPERTY_USERNAME_ATTRIBUTE, $usernameAttribute);
        return $this;
    }

    /**
     *
     * @return string
     */
    public static function get_table_name()
    {
        return 'RegisteredServiceImpl';
    }
}
