<?php
namespace Chamilo\Application\CasStorage\Service\Storage\DataClass;

use Chamilo\Application\CasStorage\Service\Storage\DataManager;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @author Hans De Bisschop
 */
class Service extends DataClass
{

    /**
     * CasAccount properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_SERVICE_ID = 'serviceId';
    const PROPERTY_THEME = 'theme';
    const PROPERTY_ALLOWED_TO_PROXY = 'allowedToProxy';
    const PROPERTY_ANONYMOUS_ACCESS = 'anonymousAccess';
    const PROPERTY_EVALUATION_ORDER = 'evaluation_order';
    const PROPERTY_IGNORE_ATTRIBUTES = 'ignoreAttributes';
    const PROPERTY_SSO_ENABLED = 'ssoEnabled';
    const PROPERTY_ENABLED = 'enabled';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_NAME,
                self :: PROPERTY_DESCRIPTION,
                self :: PROPERTY_SERVICE_ID,
                self :: PROPERTY_THEME,
                self :: PROPERTY_ALLOWED_TO_PROXY,
                self :: PROPERTY_ANONYMOUS_ACCESS,
                self :: PROPERTY_EVALUATION_ORDER,
                self :: PROPERTY_IGNORE_ATTRIBUTES,
                self :: PROPERTY_SSO_ENABLED,
                self :: PROPERTY_ENABLED));
    }

    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    public function get_service_id()
    {
        return $this->get_default_property(self :: PROPERTY_SERVICE_ID);
    }

    public function set_service_id($service_id)
    {
        $this->set_default_property(self :: PROPERTY_SERVICE_ID, $service_id);
    }

    public function get_theme()
    {
        return $this->get_default_property(self :: PROPERTY_THEME);
    }

    public function set_theme($theme)
    {
        $this->set_default_property(self :: PROPERTY_THEME, $theme);
    }

    public function get_allowed_to_proxy()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOWED_TO_PROXY);
    }

    public function set_allowed_to_proxy($allowed_to_proxy)
    {
        $this->set_default_property(self :: PROPERTY_ALLOWED_TO_PROXY, $allowed_to_proxy);
    }

    public function get_anonymous_access()
    {
        return $this->get_default_property(self :: PROPERTY_ANONYMOUS_ACCESS);
    }

    public function set_anonymous_access($anonymous_access)
    {
        $this->set_default_property(self :: PROPERTY_anonymous_access, $anonymous_access);
    }

    public function get_evaluation_order()
    {
        return $this->get_default_property(self :: PROPERTY_EVALUATION_ORDER);
    }

    public function set_evaluation_order($evaluation_order)
    {
        $this->set_default_property(self :: PROPERTY_EVALUATION_ORDER, $evaluation_order);
    }

    public function get_ignore_attributes()
    {
        return $this->get_default_property(self :: PROPERTY_IGNORE_ATTRIBUTES);
    }

    public function set_ignore_attributes($ignore_attributes)
    {
        $this->set_default_property(self :: PROPERTY_IGNORE_ATTRIBUTES, $ignore_attributes);
    }

    public function get_sso_enabled()
    {
        return $this->get_default_property(self :: PROPERTY_SSO_ENABLED);
    }

    public function set_sso_enabled($sso_enabled)
    {
        $this->set_default_property(self :: PROPERTY_SSO_ENABLED, $sso_enabled);
    }

    public function get_enabled()
    {
        return $this->get_default_property(self :: PROPERTY_ENABLED);
    }

    public function set_enabled($enabled)
    {
        $this->set_default_property(self :: PROPERTY_ENABLED, $enabled);
    }

    public static function get_table_name()
    {
        return PlatformSetting :: get('table', __NAMESPACE__);
    }

    public function get_enabled_icon()
    {
        switch ($this->get_enabled())
        {
            case self :: STATUS_ENABLED :
                $path = Theme :: getInstance()->getImagePath(
                    'Chamilo\Application\CasStorage\Service',
                    'Enabled/Enabled');
                break;
            case self :: STATUS_DISABLED :
                $path = Theme :: getInstance()->getImagePath(
                    'Chamilo\Application\CasStorage\Service',
                    'Enabled/Disabled');
                break;
        }

        return '<img src="' . $path . '" />';
    }

    public function is_enabled()
    {
        return $this->get_enabled() == self :: STATUS_ENABLED;
    }
}
