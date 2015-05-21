<?php
namespace Chamilo\Core\Repository\Common\Export\Cpo\Type;

use Chamilo\Core\Repository\Common\Export\Cpo\CpoContentObjectExport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;

class CpoDefaultContentObjectExport extends CpoContentObjectExport
{
    const PROPERTY_TAGS = 'tags';

    public function render()
    {
        $document = $this->get_export_implementation()->get_context()->get_dom_document();
        $content_object = $this->get_export_implementation()->get_content_object();
        
        $content_object_node = $this->get_export_implementation()->get_context()->get_content_object_node(
            $content_object->get_id());
        
        $export_prop = array(
            ContentObject :: PROPERTY_TYPE, 
            ContentObject :: PROPERTY_OBJECT_NUMBER, 
            ContentObject :: PROPERTY_PARENT_ID, 
            ContentObject :: PROPERTY_TITLE, 
            ContentObject :: PROPERTY_DESCRIPTION, 
            ContentObject :: PROPERTY_COMMENT, 
            ContentObject :: PROPERTY_CREATION_DATE, 
            ContentObject :: PROPERTY_MODIFICATION_DATE);
        
        $general = $document->createElement('general');
        $content_object_node->appendChild($general);
        
        foreach ($export_prop as $prop)
        {
            $property = $document->createElement($prop);
            $general->appendChild($property);
            $text = $document->createTextNode(
                $this->get_export_implementation()->get_content_object()->get_default_property($prop));
            $text = $property->appendChild($text);
        }
        
        if (! $this->get_export_implementation()->get_context()->get_parameters()->has_categories() || $content_object->get_owner_id() !=
             $this->get_export_implementation()->get_context()->get_parameters()->get_user())
        {
            $parent_node = $this->get_export_implementation()->get_context()->get_dom_xpath()->query(
                'parent_id', 
                $general)->item(0);
            $new_parent_node = $document->createElement('parent_id');
            $new_parent_node->appendChild($document->createTextNode(0));
            $general->replaceChild($new_parent_node, $parent_node);
        }
        
        $tags = DataManager :: retrieve_content_object_tags_for_content_object($content_object->get_id());
        if (count($tags))
        {
            $property_tags = $document->createElement(self :: PROPERTY_TAGS);
            $general->appendChild($property_tags);
            $tags_content = $document->createTextNode(implode(',', $tags));
            $property_tags->appendChild($tags_content);
        }
        
        $extended = $document->createElement('extended');
        $content_object_node->appendChild($extended);
        
        foreach ($content_object->get_additional_properties() as $prop => $value)
        {
            $property = $document->createElement($prop);
            $extended->appendChild($property);
            $text = $document->createTextNode(convert_uuencode($value));
            $text = $property->appendChild($text);
        }
        
        $this->external_sync();
    }
}
