<?php
namespace Chamilo\Core\Repository\Package;

use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.install
 */

/**
 * This installer can be used to create the storage structure for the repository.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function extra()
    {
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
     * @return \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    protected function getRightsService()
    {
        $dependencyInjectionContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $dependencyInjectionContainer->get(RightsService::class);
    }
}
