<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * @package Chamilo\Core\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_EDIT = 'Editor';
    public const ACTION_MOVE = 'Mover';
    public const ACTION_RIGHTS = 'Rights';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_DIRECTION = 'direction';
    public const PARAM_DIRECTION_DOWN = 'down';
    public const PARAM_DIRECTION_UP = 'up';
    public const PARAM_ITEM = 'item';
    public const PARAM_PARENT = 'parent';
    public const PARAM_TYPE = 'type';

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::CONTEXT);
    }

    /**
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->getUrlGenerator()->fromParameters([Application::PARAM_ACTION => Manager::ACTION_BROWSE]);
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService()
    {
        return $this->getService(ItemService::class);
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService(RightsService::class);
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
