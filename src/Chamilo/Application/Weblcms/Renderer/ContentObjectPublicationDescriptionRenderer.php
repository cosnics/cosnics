<?php
namespace Chamilo\Application\Weblcms\Renderer;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\ContentObjectDescriptionRenderer;

/**
 * This class renders the description of a given content object publication.
 * This class is used to determine the correct
 * attachment url.
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationDescriptionRenderer extends ContentObjectDescriptionRenderer
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The content object publication
     * 
     * @var ContentObjectPublication
     */
    private $publication;

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param ContentObjectPublicationListRenderer $parent
     * @param mixed[string] $publication
     */
    public function __construct(ContentObjectPublicationListRenderer $parent, $publication)
    {
        $content_object = $parent->get_content_object_from_publication($publication);
        
        parent :: __construct($parent, $content_object);
        
        $this->publication = $publication;
    }

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the attachment url
     * 
     * @param ContentObject $attachment
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->parent->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW_ATTACHMENT, 
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_OBJECT_ID => $attachment->get_id(), 
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $this->publication[ContentObjectPublication :: PROPERTY_ID]));
    }
}
