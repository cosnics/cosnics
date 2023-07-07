<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class MoverComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $moveDirection = $this->getRequest()->query->get(self::PARAM_DIRECTION);

        if (is_null($moveDirection))
        {
            throw new ParameterNotDefinedException(self::PARAM_DIRECTION);
        }

        $itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifier))
        {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        $item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

        $success = $this->getCachedItemService()->moveItemInDirection($item, $moveDirection);

        $message = $this->getTranslator()->trans(
            $success ? 'ObjectMoved' : 'ObjectNotMoved',
            ['{OBJECT}' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')],
            StringUtilities::LIBRARIES
        );

        $this->redirectWithMessage(
            $message, !$success, [
                Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                Manager::PARAM_PARENT => $item->getParentId()
            ]
        );
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->getHomeUrl(), $this->getTranslator()->trans('ManagerBrowserComponent', [], 'Chamilo\Core\Menu')
            )
        );
    }
}
