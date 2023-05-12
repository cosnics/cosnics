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
                self::PARAM_ACTION => self::ACTION_ENTRY, self::PARAM_ENTITY_ID => $this->getEntityIdentifier(),
                self::PARAM_ENTITY_TYPE => $this->getEntityType()
            ]
        );

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment */
        $assignment = $this->get_root_content_object();
        $contentObjects = $assignment->getAutomaticFeedbackObjects();

        return $this->getTwig()->render(
            Manager::CONTEXT . ':CreatorConfirmation.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                'ASSIGNMENT_TITLE' => $assignment->get_title(), 'RETURN_URL' => $returnUrl,
                'SHOW_AUTOMATIC_FEEDBACK' => $assignment->isAutomaticFeedbackVisible(),
                'AUTOMATIC_FEEDBACK_TEXT' => $assignment->get_automatic_feedback_text(),
                'AUTOMATIC_FEEDBACK_CONTENT_OBJECTS' => $contentObjects,
                'ATTACHMENT_VIEWER_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT,
                        self::PARAM_ATTACHMENT_ID => '__ATTACHMENT_ID__'
                    ]
                )
            ]
        );
    }
}
