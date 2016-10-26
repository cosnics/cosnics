<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Custom publication handler for the assessment tool
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationHandler extends \Chamilo\Application\Weblcms\Publication\ContentObjectPublicationHandler
{
    /**
     * Creates a content object publication for a given content object
     *
     * @param ContentObject $contentObject
     *
     * @return ContentObjectPublication
     */
    protected function createPublicationForContentObject($contentObject)
    {
        $publication = parent::createPublicationForContentObject($contentObject);

        if ($publication instanceof ContentObjectPublication)
        {
            $assessmentPublication = new Publication();
            $assessmentPublication->set_publication_id($publication->getId());
            $assessmentPublication->set_show_score(1);
            $assessmentPublication->set_show_correction(1);
            $assessmentPublication->set_show_answer_feedback(Configuration::ANSWER_FEEDBACK_TYPE_ALL);
            $assessmentPublication->set_feedback_location(Configuration::FEEDBACK_LOCATION_TYPE_BOTH);

            if (!$assessmentPublication->create())
            {
                throw new \RuntimeException(
                    'Could not create an assessment publication for publication with id ' . $publication->getId()
                );
            }
        }

        return $publication;
    }
}