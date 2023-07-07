<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager implements DelegateComponent
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $itemType = $this->getRequest()->query->get(self::PARAM_TYPE);

        if (is_null($itemType))
        {
            throw new ParameterNotDefinedException(self::PARAM_TYPE);
        }

        $itemRenderer = $this->getItemRendererFactory()->getAvailableItemRenderer($itemType);

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, $this->getTranslator()->trans(
                'AddMenuItemComponentTitle', ['{ITEM_TYPE}' => $itemRenderer->getRendererTypeName()], Manager::CONTEXT
            )
            )
        );

        $itemForm = new ItemForm(
            $itemType, $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_CREATE,
                self::PARAM_TYPE => $itemType
            ]
        )
        );

        if ($itemForm->validate())
        {
            $item = $this->getCachedItemService()->createItemForTypeFromValues(
                $itemType, $itemForm->exportValues()
            );

            $success = $item instanceof Item;

            if ($success)
            {
                $message = $this->getTranslator()->trans(
                    'ObjectCreated', ['OBJECT' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else
            {
                $message = $this->getTranslator()->trans(
                    'ObjectNotCreated',
                    ['OBJECT' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->redirectWithMessage(
                $message, !$success, [
                    Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                    Manager::PARAM_PARENT => $item->getParentId()
                ]
            );
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $itemForm->render();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
