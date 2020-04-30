<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Gallery;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\RecordGalleryTable\RecordGalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty;

/**
 * Property model for the content object publications gallery table
 * 
 * @author Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record gallery table
 */
class ObjectPublicationGalleryTablePropertyModel extends RecordGalleryTablePropertyModel
{

    /**
     * Initializes the properties for the table
     */
    public function initialize_properties()
    {
        $this->add_property(
            new DataClassGalleryTableProperty(ContentObject::class, ContentObject::PROPERTY_TITLE));
        
        $this->add_property(
            new DataClassGalleryTableProperty(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
    }
}
