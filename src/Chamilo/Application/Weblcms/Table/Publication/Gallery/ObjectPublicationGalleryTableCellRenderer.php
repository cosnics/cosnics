<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Gallery;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\RecordGalleryTable\RecordGalleryTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Utilities\Utilities;

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
     * Renders a cell
     * 
     * @param mixed $publication
     *
     * @return string
     */
    public function render_cell($publication)
    {
        $object = $this->get_component()->get_content_object_from_publication($publication);
        
        $html = array();
        
        $html[] = parent :: render_cell($publication);
        $html[] = '<h3>' . Utilities :: truncate_string($object->get_title(), 25) . '</h3>';
        
        $details_url = $this->get_component()->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID], 
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW));
        
        $thumbnail = ContentObjectRenditionImplementation :: launch(
            $object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_THUMBNAIL, 
            $this);
        
        $html[] = '<a href="' . $details_url . '">' . $thumbnail . '</a>';
        
        return implode(PHP_EOL, $html);
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
        $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
        return $toolbar->as_html();
    }
}
