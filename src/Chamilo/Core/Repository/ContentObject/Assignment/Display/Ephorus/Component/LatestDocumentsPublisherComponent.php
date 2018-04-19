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

    public function get_publication_id()
    {
        return \Chamilo\Libraries\Platform\Session\Request::get(
            \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION
        );
    }

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        if (!$this->get_publication_id())
        {
            throw new NoObjectSelectedException(Translation::get('AssignmentSubmission', null, 'weblcms'));
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                Entry::class,
                Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ),
            new StaticConditionVariable($this->get_publication_id())
        );

        $trackers = $this->getAssignmentRequestRepository()->retrieveAssignmentEntriesWithRequests(new RecordRetrievesParameters(null, $condition), Entry::class);

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
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_ASSIGNMENT_EPHORUS_REQUEST,
            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::ACTION_CREATE,
            self::PARAM_CONTENT_OBJECT_IDS => $ids
        );

        $this->redirect('', false, $parameters);
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_SOURCE, self::PARAM_PUBLICATION_ID, self::PARAM_TREE_NODE_ID);
    }
}
