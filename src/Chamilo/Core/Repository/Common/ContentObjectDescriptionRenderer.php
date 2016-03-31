<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * This class can be used as a base class to render descriptions of a content object.
 * This class can be used in a list
 * of content objects so that the correct attachment url is retrieved.
 * Extend this class to provide extra information and to provide the correct attachment url
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ContentObjectDescriptionRenderer
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The parent context (needed for url management)
     * 
     * @var mixed
     */
    protected $parent;

    /**
     * The content object
     * 
     * @var ContentObject
     */
    protected $content_object;

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param mixed $parent
     */
    public function __construct($parent, ContentObject $content_object)
    {
        $this->parent = $parent;
        $this->content_object = $content_object;
    }

    /**
     * Renders the publications description
     * 
     * @return string
     */
    public function render()
    {
        $rendition_implementation = ContentObjectRenditionImplementation :: factory(
            $this->content_object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_DESCRIPTION, 
            $this);
        
        return $rendition_implementation->render();
    }

    /**
     * **************************************************************************************************************
     * Abstract functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the attachment url
     * 
     * @param ContentObject $attachment
     *
     * @return string
     */
    abstract public function get_content_object_display_attachment_url($attachment);
}
