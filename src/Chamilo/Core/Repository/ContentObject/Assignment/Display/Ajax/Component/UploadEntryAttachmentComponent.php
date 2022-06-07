<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UploadEntryAttachmentComponent extends Manager
{
    /**
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    function run()
    {
        if (!$this->getDataProvider()->canEditAssignment())
        {
            throw new NotAllowedException();
        }

        $uploadedFile = $this->getRequest()->files->get('attachments');

        $file = new File();
        $file->set_parent_id(0);
        $title = substr(
            $uploadedFile->getClientOriginalName(), 0, - (strlen($uploadedFile->getClientOriginalExtension()) + 1)
        );

        $file->set_title($title);
        $file->set_description($uploadedFile->getClientOriginalName());
        $file->set_owner_id($this->getUser()->getId());

        $file->set_filename($uploadedFile->getClientOriginalName());
        $file->set_temporary_file_path($uploadedFile->getRealPath());

        if (!$file->create())
        {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->returnActualStatusCode();
            $jsonAjaxResult->set_result_code(500);
            $jsonAjaxResult->set_result_message(Translation::get('EntryAttachmentNotCreated'));
            $jsonAjaxResult->set_properties(array('object' => serialize($file)));
            $jsonAjaxResult->display();
        }

        $entry = $this->ajaxComponent->getEntry();
        if (!$entry instanceof Entry)
        {
            throw new ObjectNotExistException(Translation::get('Entry'));
        }

        $entryAttachment = $this->getDataProvider()->attachContentObjectToEntry($entry, $file);

        $properties = [
            'id' => $file->getId(),
            'attachment_id' => $entryAttachment->getId(),
            'filename' => $file->get_filename(),
            'user' => $this->getUser()->get_fullname(),
            'date' => DatetimeUtilities::getInstance()->formatLocaleDate(null, $file->get_creation_date())
        ];

        if ($entryAttachment instanceof
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment)
        {
            $this->getNotificationServiceBridge()->createNotificationForNewEntryAttachment(
                $this->getUser(), $entry, $entryAttachment
            );
        }

        $jsonAjaxResult = new JsonAjaxResult();
        $jsonAjaxResult->set_properties($properties);
        $jsonAjaxResult->display();
    }
}