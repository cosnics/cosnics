<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Rights\Form\RightsForm;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsComponent extends Manager implements DelegateComponent
{
    /**
     * @var \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    private $item;

    /**
     * @var int
     */
    private $itemIdentifier;

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

        $postBackUrl = new Redirect(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_RIGHTS,
                self::PARAM_ITEM => $itemIdentifier
            ]
        );

        $rightsLocation = $rightsService->findRightsLocationForItemIdentifier($itemIdentifier);

        $rightsForm = new RightsForm(
            $postBackUrl->getUrl(), $itemIdentifier != 0, $rightsService->getAvailableRights(),
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
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE,
                    Manager::PARAM_PARENT => $this->getItemParentIdentifier()
                ]
            );
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $rightsForm->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItem()
    {
        if (!isset($this->item))
        {
            $itemIdentifier = $this->getItemIdentifier();

            if ($itemIdentifier != 0)
            {
                $this->item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

                if (!$this->item instanceof Item)
                {
                    throw new ObjectNotExistException($this->getTranslator()->trans('MenuItem'), $itemIdentifier);
                }
            }
        }

        return $this->item;
    }

    /**
     * @return int
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItemIdentifier()
    {
        if (!isset($this->itemIdentifier))
        {
            $this->itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM, 0);

            if ($this->itemIdentifier != 0)
            {
                $this->getItem();
            }
        }

        return $this->itemIdentifier;
    }

    /**
     * @return int
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItemParentIdentifier()
    {
        $item = $this->getItem();

        return $item instanceof Item ? $item->getParentId() : 0;
    }
}
