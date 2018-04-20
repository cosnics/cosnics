<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class executes the ephorus submanager
 * 
 * @author Tom Goethals - Hogeschool Gent
 */
class CreatorComponent extends Manager
{

    private $publication_id;

    /**
     * Returns base requests containing the author ids
     *
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function get_base_requests()
    {
        $translation = Translation::get('AssignmentSubmission', array(), 'Chamilo\Core\Tracking');
        
        $ids = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTRY_ID);
        
        if (! $ids)
        {
            throw new NoObjectSelectedException($translation);
        }

        $ids = (array) $ids;
        
        $requests = array();
        foreach ($ids as $id)
        {
            $entry = $this->getDataProvider()->findEntryByIdentifier($id);

            $request = new Request();
            $request->set_process_type(Request::PROCESS_TYPE_CHECK_AND_INVISIBLE);
            $request->set_content_object_id($entry->getContentObjectId());
            $request->set_author_id($entry->getUserId());
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
        $parameters = array(self::PARAM_ACTION => self::ACTION_BROWSE);

        $this->redirect($message, $is_error, $parameters);
    }


    /**
     *
     * @return string
     */
    function run()
    {
        // TODO: Implement run() method.
    }
}
