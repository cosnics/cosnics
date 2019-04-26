<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;

/**
 * Creates new requests for ephorus
 */
class CreatorComponent extends Manager
{

    /**
     * @return string|void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
//        $contentObjectIds = $this->getContentObjectIdsFromSelectedEntries();

        throw new NotAllowedException();

//        $requestManager = $this->getRequestManager();
//        $failures = $requestManager->handInDocumentsByIds($contentObjectIds, $this->getUser());
//
//        $message = $this->get_result(
//            $failures,
//            count($contentObjectIds),
//            'SelectedRequestNotCreated',
//            'SelectedRequestsNotCreated',
//            'SelectedRequestCreated',
//            'SelectedRequestsCreated',
//            self::EPHORUS_TRANSLATION_CONTEXT
//        );
//
//        $this->redirect($message, $failures > 0, [self::PARAM_ACTION => self::ACTION_BROWSE]);
    }

    /**
     * Returns base requests containing the author ids
     *
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function getContentObjectIdsFromSelectedEntries()
    {
        $translation = Translation::get('Entry');

        $ids = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTRY_ID);

        if (!$ids)
        {
            throw new NoObjectSelectedException($translation);
        }

        if(!is_array($ids))
        {
            $ids = (array) $ids;
        }

        $contentObjectIds = [];
        foreach ($ids as $id)
        {
            $entry = $this->getAssignmentServiceBridge()->findEntryByIdentifier($id);
            $contentObjectIds[] = $entry->getContentObjectId();
        }

        return $contentObjectIds;
    }
}
