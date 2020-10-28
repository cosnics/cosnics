<?php

namespace Chamilo\Application\ExamAssignment\Component;

use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Format\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Format\Structure\Page;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LoginComponent
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class LoginComponent extends Manager implements NoAuthenticationSupport
{
    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        if ($this->getUser() instanceof User)
        {
            $this->redirect(null, false, [self::PARAM_ACTION => self::ACTION_LIST]);

            return null;
        }

        if($this->getRequest()->getMethod() == Request::METHOD_POST)
        {
            $this->getAuthenticationValidator()->validate();
        }

        $notAuthenticatedResponse = new NotAuthenticatedResponse();

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(
            Manager::context() . ':Login.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                'LOGIN_FORM' => $notAuthenticatedResponse->displayLoginForm()
            ]
        );
    }
}
