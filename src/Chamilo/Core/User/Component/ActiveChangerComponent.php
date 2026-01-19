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
abstract class ActiveChangerComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $userService = $this->getUserService();
        $translator = $this->getTranslator();

        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID);

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
                if (!$this->getUser()->isPlatformAdmin())
                {
                    $failures ++;
                    continue;
                }

                $user = $userService->findUserByIdentifier($id);
                $user->set_active($active);

                if (!$userService->updateUser($user))
                {
                    $failures ++;
                }
            }

            if ($active == 0)
            {
                $message = $this->get_result(
                    $failures, count($ids), 'UserNotDeactivated', 'UsersNotDeactivated', 'UserDeactivated',
                    'UsersDeactivated'
                );
            }
            else
            {
                $message = $this->get_result(
                    $failures, count($ids), 'UserNotActivated', 'UsersNotActivated', 'UserActivated', 'UsersActivated'
                );
            }

            return $this->redirectWithMessage(
                $message, ($failures > 0), [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => self::ACTION_BROWSE_USERS
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

    abstract protected function getState(): bool;
}
