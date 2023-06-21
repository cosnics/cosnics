<?php
namespace Chamilo\Core\Admin\Package;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Admin\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @throws \Exception
     */
    public function extra(): bool
    {
        $translator = $this->getTranslator();

        // Update the default settings to the database
        if (!$this->update_settings())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectsAdded', ['OBJECTS' => $translator->trans('DefaultSettings', [], Manager::CONTEXT)],
                StringUtilities::LIBRARIES
            )
            );
        }

        if (!$this->getRightsService()->createRoot())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectCreated', ['OBJECT' => $translator->trans('RightsTree, [], Manager::CONTEXT')],
                StringUtilities::LIBRARIES
            )
            );
        }

        return true;
    }

    protected function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    /**
     * @throws \Exception
     */
    public function update_settings()
    {
        $values = $this->get_form_values();

        $settings = [];
        $settings[] = ['Chamilo\Core\Admin', 'site_name', $values['site_name']];
        $settings[] = ['Chamilo\Core\Admin', 'platform_language', $values['platform_language']];
        $settings[] = ['Chamilo\Core\Admin', 'version', '1.0'];
        $settings[] = ['Chamilo\Core\Admin', 'theme', 'Aqua'];

        $settings[] = ['Chamilo\Core\Admin', 'institution', $values['organization_name']];
        $settings[] = ['Chamilo\Core\Admin', 'institution_url', $values['organization_url']];

        $settings[] = ['Chamilo\Core\Admin', 'show_administrator_data', 'true'];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_firstname', $values['admin_firstname']];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_surname', $values['admin_surname']];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_email', $values['admin_email']];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_telephone', $values['admin_phone']];

        $this->getDataClassRepositoryCache()->truncate(Setting::class);

        foreach ($settings as $setting)
        {
            $setting_object = DataManager::retrieve_setting_from_variable_name($setting[1], $setting[0]);
            $setting_object->set_context($setting[0]);
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
