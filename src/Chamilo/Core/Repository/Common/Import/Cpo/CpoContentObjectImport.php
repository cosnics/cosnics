<?php
namespace Chamilo\Core\Repository\Common\Import\Cpo;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\UUID;

class CpoContentObjectImport extends ContentObjectImport
{

    public function import()
    {
        $content_object_node =
            $this->get_import_implementation()->get_content_object_import_parameters()->get_content_object_node();
        $dom_xpath = $this->get_import_implementation()->get_controller()->get_dom_xpath();

        $content_object_type = $this->get_import_implementation()->get_controller()->determine_content_object_type(
            $dom_xpath->query('general/type', $content_object_node)->item(0)->nodeValue
        );

        $content_object = new $content_object_type();

        $content_object->set_owner_id(
            $this->get_import_implementation()->get_controller()->get_parameters()->get_user()
        );

        // default_properties
        $import_properties = array(
            ContentObject::PROPERTY_TITLE,
            ContentObject::PROPERTY_DESCRIPTION,
            ContentObject::PROPERTY_COMMENT,
            ContentObject::PROPERTY_CREATION_DATE,
            ContentObject::PROPERTY_MODIFICATION_DATE
        );

        foreach ($import_properties as $import_property)
        {
            $content_object->set_default_property(
                $import_property,
                $dom_xpath->query('general/' . $import_property, $content_object_node)->item(0)->nodeValue
            );
        }

        $content_object->set_type($content_object_type);

        $version_number = $dom_xpath->query('general/object_number', $content_object_node)->item(0)->nodeValue;
        if (!$this->get_import_implementation()->get_controller()->get_content_object_object_number_cache_id(
            $version_number
        )
        )
        {
            $this->get_import_implementation()->get_controller()->set_content_object_object_number_cache_id(
                $version_number,
                UUID::v4()
            );
        }
        $content_object->set_object_number(
            $this->get_import_implementation()->get_controller()->get_content_object_object_number_cache_id(
                $version_number
            )
        );

        $parent_id = $dom_xpath->query('general/parent_id', $content_object_node)->item(0)->nodeValue;
        if ($parent_id != 0)
        {
            $parent_id = $this->get_import_implementation()->get_controller()->get_category_id_cache_id($parent_id);
        }
        else
        {
            $parent_id = $this->get_import_implementation()->get_controller()->get_parameters()->get_category();
        }
        $content_object->set_parent_id(
            $this->get_import_implementation()->get_controller()->determine_parent_id($parent_id)
        );

        // additional_properties
        foreach ($content_object->get_additional_property_names() as $additional_property)
        {
            $content_object->set_additional_property(
                $additional_property,
                convert_uudecode(
                    $dom_xpath->query('extended/' . $additional_property, $content_object_node)->item(0)->nodeValue
                )
            );
        }

        return $content_object;
    }

    public function post_import($content_object)
    {
        $html_editor_properties = $content_object::get_html_editors();

        foreach ($html_editor_properties as $html_editor_property)
        {
            if ($html_editor_property == ContentObject::PROPERTY_DESCRIPTION)
            {
                $value = $content_object->get_description();
            }
            else
            {
                $value = $content_object->get_additional_property($html_editor_property);
            }

            $value = self::update_resources($this->get_import_implementation()->get_controller(), $value);

            if ($html_editor_property == ContentObject::PROPERTY_DESCRIPTION)
            {
                $content_object->set_description($value);
            }
            else
            {
                $content_object->set_additional_property($html_editor_property, $value);
            }
        }

        if (count($html_editor_properties) > 0)
        {
            $content_object->update();
        }
    }

    public static function update_resources($controller, $value)
    {
        /**
         * Convert encoding to preserve multi-byte UTF-8 characters as iso-8859-1 HTML entities to avoid being read
         * incorrectly by DomDocument::loadHTML().
         * Does not convert tags.
         */
        $value = mb_convert_encoding($value, 'html-entities', 'UTF-8');

        $dom_document = new \DOMDocument();
        $dom_document->loadHTML($value);

        if ($dom_document->firstChild instanceof \DOMNode)
        {
            $dom_document->removeChild($dom_document->firstChild);
            $dom_xpath = new \DOMXPath($dom_document);

            $body_nodes = $dom_xpath->query('body/*');
            $fragment = $dom_document->createDocumentFragment();
            foreach ($body_nodes as $child)
            {
                $fragment->appendChild($child);
            }
            $dom_document->replaceChild($fragment, $dom_document->firstChild);

            $resources = $dom_xpath->query('//resource');
            foreach ($resources as $resource)
            {
                $old_source = $resource->getAttribute('source');
                $new_source = $controller->get_content_object_id_cache_id($old_source);

                $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), $new_source
                );
                
                $resource->setAttribute('source', $new_source);
                $resource->setAttribute('security_code', $contentObject->calculate_security_code());
            }

            /**
             * Return iso-8859-1 encoded HTML entities to original UTF-8 encoding.
             */
            return mb_convert_encoding($dom_document->saveHTML(), 'UTF-8', 'html-entities');

            return mb_convert_encoding($dom_document->saveHTML(), 'UTF-8', 'html-entities');
        }
    }
}
