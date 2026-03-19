<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleteComponent extends Manager
{
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function run(?User $currentUser = null): Response
    {
        $translator = $this->getTranslator();
        $groupService = $this->getGroupService();
        $ids = $this->getRequest()->getFromRequestOrQuery(DataClass::PROPERTY_ID);

        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $failures = 0;

        if (!empty($ids)) {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($ids as $id) {
                $group = $groupService->findGroupByIdentifier($id);

                if (!$groupService->deleteGroup($group, $currentUser)) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($ids) == 1) {
                    $message = $translator->trans(
                        'ObjectNotDeleted', ['%Object%' => $translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
                else {
                    $message = $translator->trans(
                        'ObjectsNotDeleted', ['%Object%' => $translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
            }
            elseif (count($ids) == 1) {
                $message = $translator->trans(
                    'ObjectDeleted', ['%Object%' => $translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else {
                $message = $translator->trans(
                    'ObjectsDeleted', ['%Object%' => $translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $message, $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
        }
    }
}
