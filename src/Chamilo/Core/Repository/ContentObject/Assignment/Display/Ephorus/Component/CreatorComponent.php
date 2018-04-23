<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;

/**
 * Creates new requests for ephorus
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $requests = $this->prepareRequests();

        $requestManager = $this->getRequestManager();
        $failures = $requestManager->handInDocuments($requests);

        $message = $this->get_result(
            $failures,
            count($requests),
            'SelectedRequestNotCreated',
            'SelectedRequestsNotCreated',
            'SelectedRequestCreated',
            'SelectedRequestsCreated');

        $this->redirect($message, $failures > 0, [self::PARAM_ACTION => self::ACTION_BROWSE]);
    }

    /**
     * Returns base requests containing the author ids
     *
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function prepareRequests()
    {
        $translation = Translation::get('Entry');
        
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
            $request->set_course_id(0);
            $requests[] = $request;
        }
        
        return $requests;
    }
}
