<?php
namespace Chamilo\Core\Repository\Common\Export\Cpo\Type;

use Chamilo\Core\Repository\Common\Export\Cpo\CpoContentObjectExport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

class CpoDefaultContentObjectExport extends CpoContentObjectExport
{
    public const PROPERTY_TAGS = 'tags';

    public function render()
    {
        $document = $this->get_export_implementation()->get_context()->get_dom_document();
        $content_object = $this->get_export_implementation()->get_content_object();

        $content_object_node = $this->get_export_implementation()->get_context()->get_content_object_node(
            $content_object->get_id()
        );

        // Default properties
        $export_prop = [
            ContentObject::PROPERTY_TYPE,
            ContentObject::PROPERTY_OBJECT_NUMBER,
            ContentObject::PROPERTY_TITLE,
            ContentObject::PROPERTY_DESCRIPTION,
            ContentObject::PROPERTY_COMMENT,
            ContentObject::PROPERTY_CREATION_DATE,
            ContentObject::PROPERTY_MODIFICATION_DATE
        ];

        $general = $document->createElement('general');
        $content_object_node->appendChild($general);

        foreach ($export_prop as $prop)
        {
            $this->addGeneralProperty($document, $general, $prop);
        }

        // Process the category
        $contentObjectRelation =
            $this->getContentObjectRelationService()->getContentObjectRelationForWorkspaceAndContentObject(
                $this->get_export_implementation()->get_context()->get_parameters()->getWorkspace(),
                $this->get_export_implementation()->get_content_object()
            );

        if ($contentObjectRelation)
        {
            $this->addGeneralPropertyValue(
                $document, $general, 'parent_id', $contentObjectRelation->getCategoryId()
            );
        }
        else
        {
            $this->addGeneralProperty($document, $general, 'parent_id');
        }

        if (!$this->get_export_implementation()->get_context()->get_parameters()->has_categories() ||
            $content_object->get_owner_id() !=
            $this->get_export_implementation()->get_context()->get_parameters()->get_user())
        {
            $parent_node = $this->get_export_implementation()->get_context()->get_dom_xpath()->query(
                'parent_id', $general
            )->item(0);
            $new_parent_node = $document->createElement('parent_id');
            $new_parent_node->appendChild($document->createTextNode(0));
            $general->replaceChild($new_parent_node, $parent_node);
        }

        $extended = $document->createElement('extended');
        $content_object_node->appendChild($extended);

        foreach ($content_object->getAdditionalProperties() as $prop => $value)
        {
            $property = $document->createElement($prop);
            $extended->appendChild($property);
            $text = $document->createTextNode(convert_uuencode($value));
            $text = $property->appendChild($text);
        }
    }

    public function addGeneralProperty($document, $general, $prop)
    {
        $this->addGeneralPropertyValue(
            $document, $general, $prop,
            $this->get_export_implementation()->get_content_object()->getDefaultProperty($prop)
        );
    }

    public function addGeneralPropertyValue($document, $general, $prop, $value)
    {
        $property = $document->createElement($prop);
        $general->appendChild($property);
        $text = $document->createTextNode($value);
        $text = $property->appendChild($text);
    }
}
