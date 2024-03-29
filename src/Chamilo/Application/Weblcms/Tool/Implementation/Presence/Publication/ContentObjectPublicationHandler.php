<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Custom publication handler for the presence tool
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
            $presencePublication = new Publication();
            $presencePublication->setPublicationId($publication->getId());

            if (!$presencePublication->create())
            {
                throw new \RuntimeException(
                    'Could not create a presence publication for publication with id ' . $publication->getId());
            }
        }

        return $publication;
    }
}