<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\Form\ItemForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $item = $this->getItem();
        $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromRequest(), $this->getTranslator()->trans(
                'EditMenuItemComponentTitle', ['%ItemName%' => $itemRenderer->renderTitleForCurrentLanguage($item)],
                Manager::CONTEXT
            )
            )
        );

        $itemForm = new ItemForm(
            $item->getType(), $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::UPDATE->value,
                self::PARAM_TYPE => $item->getType(),
                self::PARAM_ITEM => $item->getId()
            ]
        )
        );

        $itemForm->setItemDefaults($item);

        if ($itemForm->validate()) {
            $success = $this->getCachedItemService()->saveItemFromValues($item, $itemForm->exportValues());

            $message = $this->getTranslator()->trans(
                $success ? 'ObjectCreated' : 'ObjectNotCreated',
                ['%Object%' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')],
                StringUtilities::LIBRARIES
            );

            return $this->redirectWithMessage(
                $message, !$success, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => ActionEnum::BROWSE->value,
                    Manager::PARAM_ITEM => $item->getParentId()
                ]
            );
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $itemForm->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getItem(): Item
    {
        $itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifier)) {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        $item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

        if (!$item instanceof Item) {
            throw new ObjectNotExistException($this->getTranslator()->trans('MenuItem'), $itemIdentifier);
        }

        return $item;
    }
}
