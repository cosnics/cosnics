<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatorConfirmationComponent extends Manager
{

    public function run()
    {
        $returnUrl = $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_BROWSE, self::PARAM_ENTITY_ID => $this->getEntityIdentifier(),
                self::PARAM_ENTITY_TYPE => $this->getEntityType()
            ]
        );

        return $this->getTwig()->render(
            Manager::context() . ':CreatorConfirmation.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                'ASSIGNMENT_TITLE' => $this->get_root_content_object()->get_title(), 'RETURN_URL' => $returnUrl
            ]
        );
    }
}
