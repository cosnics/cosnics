<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Domain\EntityTypes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Custom publication handler for the evaluation tool
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
            $evaluationPublication = new Publication();
            $evaluationPublication->setPublicationId($publication->getId());
            $evaluationPublication->setEntityType(EntityTypes::ENTITY_TYPE_USER()->getValue());
            $evaluationPublication->setOpenForStudents(false);

            if (!$evaluationPublication->create())
            {
                throw new \RuntimeException(
                    'Could not create an evaluation publication for publication with id ' . $publication->getId());
            }
        }

        return $publication;
    }
}