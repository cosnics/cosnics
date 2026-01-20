<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\UserInterface\Form\GroupMoveForm;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 */
class MoverComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Throwable
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $group_id = $this->getRequest()->query->get(self::PARAM_GROUP_ID);

        $group = $this->getGroupService()->findGroupByIdentifier($this->getRequest()->query->get(self::PARAM_GROUP_ID));

        $form = new GroupMoveForm(
            $group, $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_MOVE_GROUP,
                self::PARAM_GROUP_ID => $group_id
            ]
        )
        );

        if ($form->validate())
        {
            $success = $form->moveGroup();
            $parent = $form->getNewParent();
            $message = $translator->trans(
                $success ? 'ObjectMoved' : 'ObjectNotMoved', ['OBJECT' => $translator->trans('Group')],
                StringUtilities::LIBRARIES
            );

            return $this->redirectWithMessage(
                $message, !$success, [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS,
                    self::PARAM_GROUP_ID => $parent
                ]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $translator->trans('Group') . ': ' . $group->getName();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }
}
