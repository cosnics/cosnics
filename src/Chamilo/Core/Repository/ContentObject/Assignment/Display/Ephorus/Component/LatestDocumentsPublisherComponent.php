<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LatestDocumentsPublisherComponent extends Manager
{
    public function run()
    {
        $trackers = $this->getDataProvider()->findAssignmentEntriesWithEphorusRequests();

        $ids = [];
        foreach($trackers as $tracker)
        {
            if (!$tracker->getOptionalProperty(Request::PROPERTY_REQUEST_TIME))
            {
                $ids[] = $tracker->getDefaultProperty(Entry::PROPERTY_ID);
            }
        }

        // redirect
        $parameters = array(
            self::PARAM_ACTION => self::ACTION_CREATE,
            self::PARAM_ENTRY_ID => $ids
        );

        $this->redirect('', false, $parameters);
    }

}
