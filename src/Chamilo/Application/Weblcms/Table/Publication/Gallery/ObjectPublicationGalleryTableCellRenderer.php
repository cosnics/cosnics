<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Gallery;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\RecordGalleryTable\RecordGalleryTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;

/**
 * Cell renderer for the content object publications gallery table
 * 
 * @author Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record gallery table
 */
class ObjectPublicationGalleryTableCellRenderer extends RecordGalleryTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer::renderContent()
     */
    public function renderContent($publication)
    {
        $object = $this->get_component()->get_content_object_from_publication($publication);
        
        $details_url = $this->get_component()->get_url(
            array(
                Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                Manager::PARAM_ACTION => Manager::ACTION_VIEW));
        
        $thumbnail = ContentObjectRenditionImplementation::launch(
            $object, 
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_THUMBNAIL, 
            $this);
        
        return '<a href="' . $details_url . '">' . $thumbnail . '</a>';
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer::renderTitle()
     */
    public function renderTitle($publication)
    {
        return $this->get_component()->get_content_object_from_publication($publication)->get_title();
    }

    /**
     * Returns the actions toolbar
     * 
     * @param mixed $publication
     *
     * @return String
     */
    public function get_actions($publication)
    {
        $toolbar = $this->get_component()->get_publication_actions($publication, false);
        return $toolbar->as_html();
    }
}
