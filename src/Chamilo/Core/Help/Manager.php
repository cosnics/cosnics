<?php
namespace Chamilo\Core\Help;

use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Core\Help\Service\HelpService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;

/**
 * @package Chamilo\Core\Help
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE_HELP_ITEMS = 'Browser';
    public const ACTION_UPDATE_HELP_ITEM = 'Updater';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_HELP_ITEMS;

    public const PARAM_HELP_ITEM = 'help_item';

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::CONTEXT);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    protected function getHelpService(): HelpService
    {
        return $this->getService(HelpService::class);
    }
}
