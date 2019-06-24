<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\AcceptInviteFormType;
use Chamilo\Core\User\Manager;

/**
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AcceptInviteComponent extends Manager
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

        return $this->getTwig()->render(
            Manager::context() . ':AcceptInvite.html.twig',
            ['HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'FORM' => $form->createView()]
        );
    }
}