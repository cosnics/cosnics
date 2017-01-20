<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: attachment_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.component
 */
class AttachmentViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add_help('courses general');
        
        $failed = false;
        $error_message = '';
        
        // retrieve the publication
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $publication_id);
        if (is_null($publication_id))
        {
            throw new ParameterNotDefinedException(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        }
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $publication_id);
        
        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(Translation::get('Publication'), $publication_id);
        }
        
        // retrieve the attachment
        $object_id = Request::get('object_id');
        
        if (is_null($object_id))
        {
            $failed = true;
            $error_message = Translation::get('NoObjectSelected');
        }
        
        // Let the parent decide where the object is attached
        if (method_exists($this->get_parent(), 'is_object_attached_in_context'))
        {
            if (! $this->get_parent()->is_object_attached_in_context($this))
            {
                $failed = true;
                $error_message = Translation::get('WrongObjectSelected');
            }
        } // Default the attachment is attached to the content object of the
          // publication
        else
        {
            if (! $publication->get_content_object()->is_attached_to_or_included_in($object_id))
            
            {
                $failed = true;
                $error_message = Translation::get('WrongObjectSelected');
            }
        }
        
        // Is the view right granted on the publication?
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            $failed = true;
            $error_message = Translation::get('NotAllowed');
        }
        
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        
        if (! $failed)
        {
            $trail->add(
                new Breadcrumb(
                    $this->get_url(array('object' => $object_id)), 
                    Translation::get('ViewAttachment', null, \Chamilo\Core\Repository\Manager::context())));
            
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $object_id);
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = ContentObjectRenditionImplementation::launch(
                $object, 
                ContentObjectRendition::FORMAT_HTML, 
                ContentObjectRendition::VIEW_FULL, 
                $this);
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $this->display_error_message($error_message);
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
