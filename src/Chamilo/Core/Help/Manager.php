<?php
namespace Chamilo\Core\Help;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Help\Service\HelpService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

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

    protected function getHelpService(): HelpService
    {
        return $this->getService(HelpService::class);
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
