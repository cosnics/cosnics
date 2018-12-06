<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Publication;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
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
            $assignmentPublication = new Publication();
            $assignmentPublication->setPublicationId($publication->getId());
            $assignmentPublication->setEntityType(Entry::ENTITY_TYPE_USER);

            if (! $assignmentPublication->create())
            {
                throw new \RuntimeException(
                    'Could not create an assignment publication for publication with id ' . $publication->getId());
            }
        }
        
        return $publication;
    }
}