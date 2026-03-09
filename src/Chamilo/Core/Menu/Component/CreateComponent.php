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
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreateComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
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

        $itemType = $this->getRequest()->query->get(self::PARAM_TYPE);

        if (is_null($itemType)) {
            throw new ParameterNotDefinedException(self::PARAM_TYPE);
        }

        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($itemType);

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromRequest(), $this->getTranslator()->trans(
                'AddMenuItemComponentTitle', ['%ItemType%' => $itemRenderer->getRendererTypeName()], Manager::CONTEXT
            )
            )
        );

        $itemForm = new ItemForm(
            $itemType, $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::CREATE->value,
                self::PARAM_TYPE => $itemType
            ]
        )
        );

        if ($itemForm->validate()) {
            $item = $this->getCachedItemService()->createItemForTypeFromValues(
                $itemType, $itemForm->exportValues()
            );

            $success = $item instanceof Item;

            if ($success) {
                $message = $this->getTranslator()->trans(
                    'ObjectCreated', ['%Object%' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else {
                $message = $this->getTranslator()->trans(
                    'ObjectNotCreated',
                    ['%Object%' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            return $this->redirectWithMessage(
                $message, !$success, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => ActionEnum::BROWSE->value,
                    Manager::PARAM_PARENT => $item->getParentId()
                ]
            );
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $itemForm->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }
}
