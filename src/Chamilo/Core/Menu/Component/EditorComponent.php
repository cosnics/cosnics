<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
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
class EditorComponent extends Manager implements DelegateComponent
{
    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Exception
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $item = $this->getItem();
        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($item);

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, $this->getTranslator()->trans(
                'EditMenuItemComponentTitle', ['{ITEM_NAME}' => $itemRenderer->renderTitleForCurrentLanguage($item)], Manager::CONTEXT
            )
            )
        );

        $itemForm = new ItemForm(
            $item->getType(), $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_CREATE,
                self::PARAM_TYPE => $item->getType(),
                self::PARAM_ITEM => $item->getId()
            ]
        )
        );

        $itemForm->setItemDefaults($item);

        if ($itemForm->validate())
        {
            $success = $this->getItemService()->saveItemFromValues($item, $itemForm->exportValues());

            $message = $this->getTranslator()->trans(
                $success ? 'ObjectCreated' : 'ObjectNotCreated',
                ['OBJECT' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')],
                StringUtilities::LIBRARIES
            );

            $this->redirectWithMessage(
                $message, !$success,
                [Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_ITEM => $item->getParentId()]
            );
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $itemForm->render();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    protected function getItem(): Item
    {
        $itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifier))
        {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        $item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

        if (!$item instanceof Item)
        {
            throw new ObjectNotExistException($this->getTranslator()->trans('MenuItem'), $itemIdentifier);
        }

        return $item;
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->getService(ItemRendererFactory::class);
    }
}
