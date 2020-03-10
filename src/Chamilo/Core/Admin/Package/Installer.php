<?php
namespace Chamilo\Core\Admin\Package;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocation;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package admin.install
 */

/**
 * This installer can be used to create the storage structure for the users application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Runs the install-script.
     */
    public function extra()
    {
        // Update the default settings to the database
        if (!$this->update_settings())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, Translation::get(
                'ObjectsAdded', array('OBJECTS' => Translation::get('DefaultSettings')), Utilities::COMMON_LIBRARIES
            )
            );
        }

        $location = $this->getRightsService()->createRoot(true);

        if (!$location instanceof RightsLocation)
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, Translation::get(
                'ObjectCreated', array('OBJECT' => Translation::get('RightsTree')), Utilities::COMMON_LIBRARIES
            )
            );
        }

        return true;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\RightsService
     */
    protected function getRightsService()
    {
        $dependencyInjectionContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $dependencyInjectionContainer->get(RightsService::class);
    }

    public function update_settings()
    {
        $values = $this->get_form_values();

        $settings = array();
        $settings[] = array('Chamilo\Core\Admin', 'site_name', $values['site_name']);
        $settings[] = array('Chamilo\Core\Admin', 'platform_language', $values['platform_language']);
        $settings[] = array('Chamilo\Core\Admin', 'version', '1.0');
        $settings[] = array('Chamilo\Core\Admin', 'theme', 'Aqua');

        $settings[] = array('Chamilo\Core\Admin', 'institution', $values['organization_name']);
        $settings[] = array('Chamilo\Core\Admin', 'institution_url', $values['organization_url']);

        $settings[] = array('Chamilo\Core\Admin', 'show_administrator_data', 'true');
        $settings[] = array('Chamilo\Core\Admin', 'administrator_firstname', $values['admin_firstname']);
        $settings[] = array('Chamilo\Core\Admin', 'administrator_surname', $values['admin_surname']);
        $settings[] = array('Chamilo\Core\Admin', 'administrator_email', $values['admin_email']);
        $settings[] = array('Chamilo\Core\Admin', 'administrator_telephone', $values['admin_phone']);

        DataClassCache::truncate(Setting::class_name());

        foreach ($settings as $setting)
        {
            $setting_object = DataManager::retrieve_setting_from_variable_name($setting[1], $setting[0]);
            $setting_object->set_application($setting[0]);
            $setting_object->set_variable($setting[1]);
            $setting_object->set_value($setting[2]);

            if (!$setting_object->update())
            {
                return false;
            }
        }

        return true;
    }
}
