<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ChangeUserComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);

        if ($userIdentifier)
        {
            $session = $this->getSession();

            $checkurl = $session->get('checkChamiloURL');

            $session->clear();
            $session->set(Manager::SESSION_USER_ID, $userIdentifier);
            $session->set('_as_admin', $this->getUser()->getId());
            $session->set('checkChamiloURL', $checkurl);

            $loginApplication =
                $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'page_after_login']);

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters([Application::PARAM_CONTEXT => $loginApplication])
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_USERS]),
                $this->getTranslator()->trans('AdminUserBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }

}
