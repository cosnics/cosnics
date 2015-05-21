<?php
/**
 * Created by PhpStorm.
 * User: tomgoethals Date: 12/03/14 Time: 15:35
 */
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\Repository\Form\TagsFormBuilder;

class MetadataObjectFilter
{

    /**
     *
     * @var \core\metadata\element\storage\data_class\Element[]
     */
    private $md_elements;

    private function get_dynamic_element_value_array(MetadataFilterForm $form, array $objects)
    {
        // $elements = $this->get_dynamic_metadata_elements();
        $this->md_elements = array();
        $elements = \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Type\Storage\DataManager :: get_common_metadata_elements(
            $objects);

        $element_values = array();
        foreach ($elements as $element)
        {
            $name = $this->get_element_form_name($element);
            $value = $form->exportValue($name);

            $has_controlled_vocabulary = \Chamilo\Core\Metadata\Element\Storage\DataManager :: element_has_controlled_vocabulary(
                $element->get_id());

            if ((is_numeric($value) || ! empty($value)) &&
                 (! $has_controlled_vocabulary || (is_numeric($value) && $value > 0)))
            {
                $element_values[$element->get_id()] = $value;
                $this->md_elements[$element->get_id()] = $element;
            }
        }

        return $element_values;
    }

    private function filter_objects_on_dynamic_values(array $objects, array $element_values)
    {
        $filtered_objects = array();
        foreach ($objects as $obj)
        {
            $include = true;
            $obj_values = \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Storage\DataManager :: retrieve_element_values_for_content_object_as_array(
                $obj->get_ref());
            $obj_element_values = array();

            foreach ($obj_values as $obj_value)
            {
                if (in_array($obj_value->get_element_id(), array_keys($this->md_elements)))
                {
                    $element = $this->md_elements[$obj_value->get_element_id()];
                    $voc = \Chamilo\Core\Metadata\Element\Storage\DataManager :: retrieve_controlled_vocabulary_terms_from_element(
                        $element->get_id());
                    if (sizeof($voc) == 0)
                    {
                        $obj_element_values[$obj_value->get_element_id()] = $obj_value->get_value();
                    }
                    else
                    {
                        $obj_element_values[$obj_value->get_element_id()] = $obj_value->get_element_vocabulary_id();
                    }
                }
            }
            foreach ($element_values as $element_id => $value)
            {
                if ($obj_element_values[$element_id] != $value)
                {
                    $include = false;
                }
            }

            if ($include)
            {
                $filtered_objects[] = $obj;
            }
        }

        return $filtered_objects;
    }

    private function filter_objects_on_static_values(MetadataFilterForm $form, array $objects)
    {
        $filtered_objects = array();

        $tags = $form->exportValue(TagsFormBuilder :: PROPERTY_TAGS);

        if ($tags != '')
        {
            $tags_array = explode(',', $tags);

            foreach ($objects as $obj)
            {
                $include = true;
                $obj_tags = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_tags_for_content_object(
                    $obj->get_ref());
                foreach ($tags_array as $tag)
                {
                    if (! in_array($tag, $obj_tags))
                    {
                        $include = false;
                    }
                }

                if ($include)
                {
                    $filtered_objects[] = $obj;
                }
            }
        }
        else
        {
            $filtered_objects = $objects;
        }

        return $filtered_objects;
    }

    public function get_filtered_objects(MetadataFilterForm $form, array $objects)
    {
        // $objects = $this->get_objects();
        if ($form->validate())
        {
            // form is validated, check dynamic elements first
            $element_values = $this->get_dynamic_element_value_array($form, $objects);

            if (sizeof($element_values) > 0)
            {
                $objects = $this->filter_objects_on_dynamic_values($objects, $element_values);
            }

            $objects = $this->filter_objects_on_static_values($form, $objects);

            return $objects;
        }
        else
        {
            // nothing in the form, just return all questions
            return $objects;
        }
    }

    /**
     * Returns the element form name
     *
     * @param Element $element
     *
     * @return string
     */
    public function get_element_form_name(Element $element)
    {
        return 'md' . '$' . $element->render_name();
    }
}