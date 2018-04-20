<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class LatestDocumentsPublisherComponent extends Manager
{
    public function run()
    {
        $trackers = $this->getDataProvider()->findAssignmentEntriesWithRequests();

        $ids = array();
        foreach($trackers as $tracker)
        {
            if (!$tracker->get_optional_property(Request::PROPERTY_REQUEST_TIME))
            {
                $ids[] = $tracker->get_default_property(Entry::PROPERTY_ID);
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
