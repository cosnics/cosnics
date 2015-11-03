<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsCacheService extends DoctrineFilesystemCacheService
{

    public function fillCache()
    {
        $settings = DataManager :: retrieves(Setting :: class_name(), new DataClassRetrievesParameters());

        while ($setting = $settings->next_result())
        {
            $this->settings[$setting->get_application()][$setting->get_variable()] = $setting->get_value();
        }

        return $this->getCacheProvider()->save($this->getCacheIdentifier(), $this->settings);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Configuration';
    }

    public function getCacheIdentifier()
    {
        return 'configuration.settings';
    }
}