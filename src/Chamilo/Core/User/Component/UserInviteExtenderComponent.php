<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\NoPackageBreadcrumbGenerator;

/**
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserInviteExtenderComponent extends Manager
{
    const PARAM_USER_INVITE_ID = 'UserInviteId';

    /**
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        if (!$this->areInvitesAllowed())
        {
            throw new NotAllowedException();
        }

        try
        {
            $userInvite = $this->getInviteService()->getUserInviteById(
                $this->getRequest()->getFromUrl(self::PARAM_USER_INVITE_ID)
            );

            $this->getInviteService()->extendUserInvite($this->getUser(), $userInvite);

            $success = true;
            $message = 'ExtendUserInviteSuccess';
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            $success = false;
            $message = 'ExtendUserInviteFailed';
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()), !$success,
            [self::PARAM_ACTION => self::ACTION_INVITE]
        );

        return null;
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
     */
    public function get_breadcrumb_generator()
    {
        return new NoPackageBreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

}
