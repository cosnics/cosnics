<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MoverComponent extends Manager
{

    /**
     * @return string|void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
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

        $success = $this->getItemService()->moveItemInDirection($item, $moveDirection);

        $message = $this->getTranslator()->trans(
            $success ? 'ObjectMoved' : 'ObjectNotMoved',
            array('OBJECT' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')),
            Utilities::COMMON_LIBRARIES
        );

        $this->redirect(
            $message, ($success ? false : true), array(
                Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $item->getParentId()
            )
        );
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->getHomeUrl(), $this->getTranslator()->trans('ManagerBrowserComponent', [], 'Chamilo\Core\Menu')
            )
        );
        $breadcrumbtrail->add_help('menu_mover');
    }
}
