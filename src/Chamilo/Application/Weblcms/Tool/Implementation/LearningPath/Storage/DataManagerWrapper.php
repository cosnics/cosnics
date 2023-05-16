<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage;

/**
 * Wrapper for the DataManager to be used in services
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManagerWrapper
{
    public function getPublicationTargetUserIds($publicationId)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::getPublicationTargetUserIds($publicationId, null);
    }
}