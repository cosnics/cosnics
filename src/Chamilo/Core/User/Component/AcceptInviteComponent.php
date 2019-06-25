<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\Type\AcceptInviteFormType;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AcceptInviteComponent extends Manager implements NoAuthenticationSupport
{

    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $form = $this->getForm()->create(AcceptInviteFormType::class);

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(
            Manager::context() . ':AcceptInvite.html.twig',
            ['HEADER' => $this->render_header(''), 'FOOTER' => $this->render_footer(), 'FORM' => $form->createView()]
        );
    }
}