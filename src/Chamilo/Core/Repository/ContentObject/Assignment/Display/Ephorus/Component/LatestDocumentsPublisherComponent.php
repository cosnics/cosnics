<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LatestDocumentsPublisherComponent extends Manager
{
    /**
     * @return string|void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        throw new NotAllowedException();

//        $trackers = $this->getEphorusServiceBridge()->findAssignmentEntriesWithEphorusRequests();
//
//        $ids = array();
//        foreach($trackers as $tracker)
//        {
//            if (!$tracker->get_optional_property(Request::PROPERTY_REQUEST_TIME))
//            {
//                $ids[] = $tracker->get_default_property(Entry::PROPERTY_ID);
//            }
//        }
//
//        // redirect
//        $parameters = array(
//            self::PARAM_ACTION => self::ACTION_CREATE,
//            self::PARAM_ENTRY_ID => $ids
//        );
//
//        $this->redirect('', false, $parameters);
    }

}
