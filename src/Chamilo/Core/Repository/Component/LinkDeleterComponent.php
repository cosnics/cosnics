<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\Link\LinkTable;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: link_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a link to a content object
 */
class LinkDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $type = Request :: get(self :: PARAM_LINK_TYPE);
        $object_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        $link_ids = Request :: get(self :: PARAM_LINK_ID);

        if (! is_array($link_ids))
        {
            $link_ids = array($link_ids);
        }

        if ($type && $object_id && count($link_ids) > 0)
        {
            switch ($type)
            {
                case LinkTable :: TYPE_PUBLICATIONS :
                    list($message, $is_error_message) = $this->delete_publication($object_id, $link_ids);
                    break;
                case LinkTable :: TYPE_PARENTS :
                    list($message, $is_error_message) = $this->delete_complex_wrapper($link_ids);
                    break;
                case LinkTable :: TYPE_CHILDREN :
                    list($message, $is_error_message) = $this->delete_complex_wrapper($link_ids);
                    break;
                case LinkTable :: TYPE_ATTACHED_TO :
                    list($message, $is_error_message) = $this->delete_attacher($object_id, $link_ids);
                    break;
                case LinkTable :: TYPE_ATTACHES :
                    list($message, $is_error_message) = $this->delete_attachment($object_id, $link_ids);
                    break;
            }

            $this->redirect(
                $message,
                $is_error_message,
                array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS,
                    self :: PARAM_CONTENT_OBJECT_ID => $object_id));
        }
        else
        {
            return $this->display_error_page(
                Translation :: get(
                    'NoObjectSelected',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES));
        }
    }

    public function delete_publication($object_id, $link_ids)
    {
        $failures = 0;

        foreach ($link_ids as $link_id)
        {
            list($application, $publication_id) = explode("|", $link_id);
            if (! DataManager :: delete_content_object_publication($application, $publication_id))
                $failures ++;
        }

        $message = $this->get_result(
            $failures,
            count($link_ids),
            'PublicationNotDeleted',
            'PublicationsNotDeleted',
            'PublicationDeleted',
            'PublicationsDeleted');

        return array($message, ($failures > 0));
    }

    public function delete_complex_wrapper($link_ids)
    {
        $failures = 0;

        foreach ($link_ids as $link_id)
        {
            $item = DataManager :: retrieve_complex_content_object_item($link_id);
            $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $item->get_ref());

            if (! $item->delete())
            {
                $failures ++;
                continue;
            }

            if (in_array($object->get_type(), DataManager :: get_active_helper_types()))
            {
                if (! $object->delete())
                {
                    $failures ++;
                }
            }
        }

        $message = $this->get_result(
            $failures,
            count($link_ids),
            'ComplexContentObjectItemNotDeleted',
            'ComplexContentObjectItemsNotDeleted',
            'ComplexContentObjectItemDeleted',
            'ComplexContentObjectItemsDeleted');

        return array($message, ($failures > 0));
    }

    public function delete_attachment($object_id, $link_ids)
    {
        $failures = 0;

        foreach ($link_ids as $link_id)
        {
            $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $object_id);
            if (! $object->detach_content_object($link_id, ContentObject :: ATTACHMENT_NORMAL))
                $failures ++;
        }

        $message = $this->get_result(
            $failures,
            count($link_ids),
            'AttachmentNotDeleted',
            'AttachmentsNotDeleted',
            'AttachmentDeleted',
            'AttachmentsDeleted');

        return array($message, ($failures > 0));
    }

    public function delete_attacher($object_id, $link_ids)
    {
        $failures = 0;

        foreach ($link_ids as $link_id)
        {
            $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $link_id);
            if (! $object->detach_content_object($object_id, ContentObject :: ATTACHMENT_NORMAL))
                $failures ++;
        }

        $message = $this->get_result(
            $failures,
            count($link_ids),
            'AttacherNotDeleted',
            'AttachersNotDeleted',
            'AttacherDeleted',
            'AttachersDeleted');

        return array($message, ($failures > 0));
    }

    public function delete_include($object_id, $link_ids)
    {
        $failures = 0;

        foreach ($link_ids as $link_id)
        {
        }

        $message = $this->get_result(
            $failures,
            count($link_ids),
            'PublicationNotDeleted',
            'PublicationsNotDeleted',
            'PublicationDeleted',
            'PublicationsDeleted');

        return array($message, ($failures > 0));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS,
                        self :: PARAM_CONTENT_OBJECT_ID => Request :: get(self :: PARAM_CONTENT_OBJECT_ID))),
                Translation :: get('RepositoryManagerViewerComponent')));
        $breadcrumbtrail->add_help('repository_link_deleter');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_ID, self :: PARAM_LINK_TYPE, self :: PARAM_LINK_ID);
    }
}
