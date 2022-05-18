<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Michael Kyndt
 */
class AttachmentViewerComponent extends Manager
{

    public function run()
    {
        /*
         * Retrieve data and check if it is a valid attachment
         */
        $attachment_id = Request::get(Manager::PARAM_ATTACHMENT_ID);
        
        if (is_null($attachment_id))
        {
            throw new ParameterNotDefinedException(
                Manager::PARAM_ATTACHMENT_ID);
        }
        
        $complex_content_object_item = $this->get_parent()->get_selected_complex_content_object_item();
        $reference_content_object_id = $complex_content_object_item->get_ref();
        $reference_content_object = DataManager::retrieve_by_id(
            ContentObject::class,
            $reference_content_object_id);
        
        if (DataManager::is_helper_type($reference_content_object->get_type()))
        {
            $reference_content_object_id = $reference_content_object->getAdditionalProperty('reference_id');
            $reference_content_object = DataManager::retrieve_by_id(
                ContentObject::class,
                $reference_content_object_id);
        }
        
        $attachment = DataManager::retrieve_by_id(
            ContentObject::class,
            $attachment_id);
        
        if (! $reference_content_object->is_attached_to_or_included_in($attachment_id))
        {
            throw new NotAllowedException();
        }
        
        /*
         * Render the attachment
         */
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(Manager::PARAM_ATTACHMENT_ID => $attachment_id)),
                Translation::get('ViewAttachment')));
        
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        
        $html = [];
        
        $html[] = $this->render_header();
        $html[] = '<a href="javascript:history.go(-1)">' .
             Translation::get('Back', null, Utilities::COMMON_LIBRARIES) . '</a><br /><br />';
        $html[] = ContentObjectRenditionImplementation::launch(
            $attachment, 
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_FULL, 
            $this);
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }
}
