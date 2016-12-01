<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager\Implementation\DoctrineExtension;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class AssignmentLatestDocumentsPublisherComponent extends Manager
{

    public function get_publication_id()
    {
        return \Chamilo\Libraries\Platform\Session\Request::get(
            \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
    }

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        if (! $this->get_publication_id())
        {
            throw new NoObjectSelectedException(Translation::get('AssignmentSubmission', null, 'weblcms'));
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->get_publication_id()));
        
        $doctrineExtension = new DoctrineExtension(DataManager::getInstance());
        $trackers = $doctrineExtension->retrieve_results_by_assignment(new DataClassRetrievesParameters($condition));
        
        $ids = array();
        while (($tracker = $trackers->next_result()) != null)
        {
            if (! $tracker->get_optional_property(Request::PROPERTY_REQUEST_TIME))
            {
                $ids[] = $tracker->get_default_property(AssignmentSubmission::PROPERTY_ID);
            }
        }
        
        // redirect
        $parameters = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_ASSIGNMENT_EPHORUS_REQUEST, 
            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::ACTION_CREATE, 
            self::PARAM_CONTENT_OBJECT_IDS => $ids);
        
        $this->redirect('', false, $parameters);
    }
}
