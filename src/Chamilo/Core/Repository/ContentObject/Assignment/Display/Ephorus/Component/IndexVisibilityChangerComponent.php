<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * This class executes the ephorus submanager
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class IndexVisibilityChangerComponent extends Manager
{
    /**
     * Runs this component
     */
    public function run()
    {
        $ephorusRequestGuids = $this->getRequestGuidsFromSelectedEntries();

        $requestManager = $this->getRequestManager();
        $failures = $requestManager->changeVisibilityOfDocumentsOnIndex($ephorusRequestGuids);

        $message = $this->get_result(
            $failures,
            count($ephorusRequestGuids),
            'VisibilityNotChanged',
            'VisibilityNotChanged',
            'VisibilityChanged',
            'VisibilityChanged',
            self::EPHORUS_TRANSLATION_CONTEXT
        );

        $this->redirect($message, $failures > 0, [self::PARAM_ACTION => self::ACTION_BROWSE]);
    }

    /**
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    protected function getRequestGuidsFromSelectedEntries()
    {
        $entryIds = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTRY_ID);
        if (empty($entryIds))
        {
            throw new NoObjectSelectedException($this->getTranslator()->trans('Entry', [], Manager::context()));
        }

        if (!is_array($entryIds))
        {
            $entryIds = [$entryIds];
        }

        $requests = $this->getDataProvider()->findEphorusRequestsForAssignmentEntries($entryIds);

        $request_guids = array();

        foreach ($requests as $request)
        {
            $request_guids[$request->get_guid()] = !$request->is_visible_in_index();
        }

        return $request_guids;
    }
}