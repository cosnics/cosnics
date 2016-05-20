<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class executes the ephorus submanager
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class AssignmentEphorusRequestComponent extends EphorusRequestComponent
{

    private $publication_id;

    /**
     * Returns base requests containing the author ids
     *
     * @return array
     */
    public function get_base_requests()
    {
        $translation = Translation :: get('AssignmentSubmission', array(), 'Chamilo\Core\Tracking');

        $ids = \Chamilo\Libraries\Platform\Session\Request :: get(Manager :: PARAM_CONTENT_OBJECT_IDS);

        if (! $ids)
        {
            $ids = $this->getRequest()->get(Manager :: PARAM_REQUEST_IDS);
        }

        if (! $ids)
        {
            throw new NoObjectSelectedException($translation);
        }
        $ids = (array) $ids;

        $tracker_table = AssignmentSubmission :: get_table_name();
        $tracker_class = AssignmentSubmission :: class_name();

        $requests = array();
        foreach ($ids as $id)
        {
            $tracker = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager :: retrieve_by_id(
                AssignmentSubmission :: class_name(),
                $id);

            if (! $tracker)
            {
                throw new ObjectNotExistException($translation, $id);
            }

            if (! $this->publication_id)
            {
                $this->publication_id = $tracker->get_publication_id();
            }

            $request = new Request();
            $request->set_process_type(Request :: PROCESS_TYPE_CHECK_AND_INVISIBLE);
            $request->set_course_id($this->get_course_id());
            $request->set_content_object_id($tracker->get_content_object_id());
            $request->set_author_id($tracker->get_user_id());
            $request->set_request_user_id($this->get_user_id());
            $requests[] = $request;
        }

        return $requests;
    }

    /**
     * Redirects after create
     *
     * @param string $message @codeCoverageIgnore
     */
    public function redirect_after_create($message, $is_error)
    {
        $parameters = array(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_ASSIGNMENT_BROWSER,
            \Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION => $this->get_publication_id());
        $this->redirect($message, $is_error, $parameters);
    }

    public function get_publication_id()
    {
        return $this->publication_id;
    }
}
