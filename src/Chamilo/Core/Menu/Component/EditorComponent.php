<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\Form\ItemForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EditorComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Exception
     */
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $item = $this->getItem();
        $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromRequest(), $this->getTranslator()->trans(
                'EditMenuItemComponentTitle', ['{ITEM_NAME}' => $itemRenderer->renderTitleForCurrentLanguage($item)],
                Manager::CONTEXT
            )
            )
        );

        $itemForm = new ItemForm(
            $item->getType(), $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_EDIT,
                self::PARAM_TYPE => $item->getType(),
                self::PARAM_ITEM => $item->getId()
            ]
        )
        );

        $itemForm->setItemDefaults($item);

        if ($itemForm->validate())
        {
            $success = $this->getCachedItemService()->saveItemFromValues($item, $itemForm->exportValues());

            $message = $this->getTranslator()->trans(
                $success ? 'ObjectCreated' : 'ObjectNotCreated',
                ['OBJECT' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')],
                StringUtilities::LIBRARIES
            );

            return $this->redirectWithMessage(
                $message, !$success, [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                    Manager::PARAM_ITEM => $item->getParentId()
                ]
            );
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $itemForm->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
}
