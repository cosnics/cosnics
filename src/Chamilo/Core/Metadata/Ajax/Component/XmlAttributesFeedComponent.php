<?php
namespace Chamilo\Core\Metadata\Ajax\Component;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataManager;

/**
 *
 * @package Chamilo\Core\Metadata\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlAttributesFeedComponent extends \Chamilo\Core\Metadata\Ajax\Manager
{

    public function run()
    {
        $attributes = DataManager :: retrieves(Attribute :: class_name());
        $attributeArray = array();

        while ($attribute = $attributes->next_result())
        {
            $attributeArray[$attribute->get_id()] = $attribute;
        }

        $properties = DataManager :: retrieves(Element :: class_name());

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<tree>', "\n";
        $this->dump_attribute_tree($attributeArray);
        $this->dump_property_tree($properties);
        echo '</tree>';
    }

    public function dump_attribute_tree($attributes)
    {
        echo '<node id="metadata_attribute" classes="category unlinked" title="attributes">', "\n";
        foreach ($attributes as $id => $attribute)
        {
            $prefix = (! is_null($attribute->get_namespace())) ? $attribute->get_namespace() . ':' : '';
            $name = $attribute->get_name();

            echo '<leaf id="attributes_' . $attribute->get_id() . '" classes="type type_cda_language" title="' . $prefix .
                 $name . '" description=""/>' . "\n";
        }
        echo '</node>', "\n";
    }

    public function dump_property_tree($properties)
    {
        echo '<node id="metadata_attribute" classes="category unlinked" title="elements">', "\n";
        while ($property = $properties->next_result())
        {
            echo '<leaf id="properties_' . $property->get_id() . '" classes="type type_cda_language" title="' .
                 $property->get_namespace() . ':' . $property->get_name() . '" description=""/>' . "\n";
        }
        echo '</node>', "\n";
    }

    public function display_attribute($attribute)
    {
        // $ns_prefix = (empty($attribute->get_namespace())) ? '' : $attribute->get_namespace() . ':';
        // return $ns_prefix . $attribute->get_value();
        return $attribute->get_value();
    }
}