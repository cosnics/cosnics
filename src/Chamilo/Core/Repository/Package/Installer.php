<?php
namespace Chamilo\Core\Repository\Package;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    public function extra(): bool
    {
        $translator = $this->getTranslator();

        if (!$this->getRightsService()->createRoot())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectCreated', [
                    'OBJECT' => $translator->trans('RightsTree', [],
                        \Chamilo\Core\Repository\Quota\Rights\Manager::CONTEXT)
                ], StringUtilities::LIBRARIES
            )
            );
        }

        return true;
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }
}
