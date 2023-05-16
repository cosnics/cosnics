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
        $requests = $this->getRequestsFromSelectedEntries();

        $requestManager = $this->getRequestManager();
        $failures = $requestManager->changeDocumentsVisibility($requests);

        $message = $this->get_result(
            $failures,
            count($requests),
            'VisibilityNotChanged',
            'VisibilityNotChanged',
            'VisibilityChanged',
            'VisibilityChanged',
            self::EPHORUS_TRANSLATION_CONTEXT
        );

        $this->redirectWithMessage($message, $failures > 0, [self::PARAM_ACTION => self::ACTION_BROWSE]);
    }

    /**
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    protected function getRequestsFromSelectedEntries()
    {
        $entryIds = $this->getRequest()->getFromRequestOrQuery(self::PARAM_ENTRY_ID);
        if (empty($entryIds))
        {
            throw new NoObjectSelectedException($this->getTranslator()->trans('Entry', [], Manager::CONTEXT));
        }

        if (!is_array($entryIds))
        {
            $entryIds = [$entryIds];
        }

        return $this->getDataProvider()->findEphorusRequestsForAssignmentEntries($entryIds);
    }
}