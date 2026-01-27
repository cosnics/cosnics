<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActiveComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $userService = $this->getUserService();
        $translator = $this->getTranslator();

        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        $active = $this->getState();

        if (!is_array($ids))
        {
            $ids = [$ids];
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                if (!$this->getUser()->isPlatformAdministrator())
                {
                    $failures ++;
                    continue;
                }

                $user = $userService->findUserByIdentifier($id);
                $user->setActive($active);

                if (!$userService->updateUser($user))
                {
                    $failures ++;
                }
            }

            if ($active == 0)
            {
                $message = $this->getResult(
                    $failures, count($ids), 'UserNotDeactivated', 'UsersNotDeactivated', 'UserDeactivated',
                    'UsersDeactivated'
                );
            }
            else
            {
                $message = $this->getResult(
                    $failures, count($ids), 'UserNotActivated', 'UsersNotActivated', 'UserActivated', 'UsersActivated'
                );
            }

            return $this->redirectWithMessage(
                $message, ($failures > 0), [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => self::ACTION_BROWSE
                ]
            );
        }
        else
        {
            return new Response(
                $this->display_error_page(
                    htmlentities(
                        $translator->trans(
                            'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                )
            );
        }
    }

    protected function getState(): bool
    {
        return (bool) $this->getRequest()->getFromQueryOrRequest(Manager::PARAM_ACTIVE, 0);
    }
}
