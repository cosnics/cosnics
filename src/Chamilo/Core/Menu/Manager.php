<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package Chamilo\Core\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_DELETE = 'Deleter';
    const ACTION_EDIT = 'Editor';
    const ACTION_MOVE = 'Mover';
    const ACTION_RIGHTS = 'Rights';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_DIRECTION = 'direction';
    const PARAM_DIRECTION_DOWN = 'down';
    const PARAM_DIRECTION_UP = 'up';
    const PARAM_ITEM = 'item';
    const PARAM_PARENT = 'parent';
    const PARAM_TYPE = 'type';

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
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

    /**
     * @return \Chamilo\Core\Admin\Core\BreadcrumbGenerator
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    /**
     * @return string
     */
    public function getHomeUrl()
    {
        $redirect = new Redirect([Application::PARAM_ACTION => Manager::ACTION_BROWSE]);

        return $redirect->getUrl();
    }
}
