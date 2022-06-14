<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Home\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttachmentViewerComponent extends Manager
{

    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();

        $failed = false;
        $error_message = '';
        
        // retrieve the parent content object
        $parent_id = Request::get(self::PARAM_PARENT_ID);
        
        if (is_null($parent_id))
        {
            throw new ParameterNotDefinedException(self::PARAM_PARENT_ID);
        }
        $parent = DataManager::retrieve_by_id(ContentObject::class, $parent_id);
        
        if (is_null($parent))
        {
            throw new ObjectNotExistException(Translation::get('Object'), $parent);
        }
        
        // retrieve the attachment
        $object_id = Request::get(self::PARAM_OBJECT_ID);
        
        if (is_null($object_id))
        {
            $failed = true;
            $error_message = Translation::get('NoAttachmentSelected');
        }
        
        $object = DataManager::retrieve_by_id(ContentObject::class, $object_id);
        
        if (is_null($object))
        {
            $failed = true;
            throw new ObjectNotExistException(Translation::get('Attachment'), $object);
        }
        
        // Default the attachment is attached to the content object of the
        // publication
        if (! $parent->is_attached_to_or_included_in($object_id))
        
        {
            $failed = true;
            $error_message = Translation::get('WrongObjectSelected');
        }
        
        $html = [];
        
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        
        if (! $failed)
        {
            $trail->add(
                new Breadcrumb(
                    $this->get_url(array('object' => $parent_id)), 
                    Translation::get('ViewAttachment', null, \Chamilo\Core\Repository\Manager::context())));
            
            $html[] = $this->render_header();
            
            $html[] = ContentObjectRenditionImplementation::launch(
                $object, 
                ContentObjectRendition::FORMAT_HTML, 
                ContentObjectRendition::VIEW_FULL, 
                $this);
            
            $html[] = $this->render_footer();
        }
        else
        {
            $html[] = $this->render_header();
            $html[] = $this->display_error_message($error_message);
            $html[] = $this->render_footer();
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Constructs the attachment url for the given attachment and the current object.
     * 
     * @param ContentObject $attachment The attachment for which the url is needed.
     * @return mixed the url, or null if no view right.
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        $object_id = Request::get(self::PARAM_OBJECT_ID);
        $object = DataManager::retrieve_by_id(ContentObject::class, $object_id);
        
        if (! $this->is_view_attachment_allowed($object))
        {
            return null;
        }
        return $this->get_url(
            array(self::PARAM_PARENT_ID => $object_id, self::PARAM_OBJECT_ID => $attachment->get_id()));
    }

    /**
     * Determines whether the object may be viewed by the current user.
     * 
     * @param ContentObject $object The content object to be tested.
     * @return boolean Whether the current user may view the content object.
     */
    public function is_view_attachment_allowed($object)
    {
        // Is the current user the owner?
        return $object->get_owner_id() == $this->get_user_id();
    }
}
