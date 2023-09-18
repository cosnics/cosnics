<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Rights\Form\RightsForm;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsComponent extends Manager implements BreadcrumbLessComponentInterface
{
    private Item $item;

    private string $itemIdentifier;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    public function run()
    {
        $rightsService = $this->getRightsService();

        $rightsService->isUserAllowedToAccessComponent($this->getUser());

        $itemIdentifier = $this->getItemIdentifier();

        $postBackUrl = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_RIGHTS,
                self::PARAM_ITEM => $itemIdentifier
            ]
        );

        $rightsLocation = $rightsService->findRightsLocationForItemIdentifier($itemIdentifier);

        $rightsForm = new RightsForm(
            $postBackUrl, $itemIdentifier != 0, $rightsService->getAvailableRights(),
            $rightsService->getAvailableEntities()
        );

        $rightsForm->setRightsDefaults(
            $this->getUser(), $rightsLocation->inherits(),
            $rightsService->getTargetUsersAndGroupsForRightsLocationAndAvailableRights($rightsLocation)
        );

        if ($rightsForm->validate())
        {
            $success = $rightsService->saveRightsConfigurationForItemIdentifierAndUserFromValues(
                $this->getItemIdentifier(), $this->getUser(), $rightsForm->exportValues()
            );

            $message = $this->getTranslator()->trans(
                $success ? 'RightsConfigured' : 'RightsNotConfigured',
                ['OBJECT' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')],
                'Chamilo\Libraries\Rights'
            );

            $this->redirectWithMessage(
                $message, !$success, [
                    Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                    Manager::PARAM_PARENT => $this->getItemParentIdentifier()
                ]
            );
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $rightsForm->render();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItem(): Item
    {
        if (!isset($this->item))
        {
            $itemIdentifier = $this->getItemIdentifier();

            if ($itemIdentifier != 0)
            {
                $item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

                if (!$item instanceof Item)
                {
                    throw new ObjectNotExistException($this->getTranslator()->trans('MenuItem'), $itemIdentifier);
                }

                $this->item = $item;
            }
        }

        return $this->item;
    }

    protected function getItemIdentifier(): string
    {
        if (!isset($this->itemIdentifier))
        {
            $this->itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM, '0');
        }

        return $this->itemIdentifier;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItemParentIdentifier(): string
    {
        return $this->getItem()->getParentId();
    }
}
