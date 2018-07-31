<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleteEntryAttachmentComponent extends Manager
{
    /**
     */
    function run()
    {
        try
        {
            if (!$this->getDataProvider()->canEditAssignment())
            {
                throw new NotAllowedException();
            }

            $entryAttachmentId = $this->getRequest()->getFromPost(self::PARAM_ENTRY_ATTACHMENT_ID);
            $entryAttachment = $this->getDataProvider()->findEntryAttachmentById($entryAttachmentId);
            if (!$entryAttachment instanceof EntryAttachment)
            {
                throw new ObjectNotExistException(
                    $this->getTranslator()->trans('AttachmentNotFound', null, Manager::context())
                );
            }

            $this->getDataProvider()->deleteEntryAttachment($entryAttachment);
        }
        catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }
}