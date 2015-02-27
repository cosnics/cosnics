<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Attribute\Entity\AttributeEntity;
use Chamilo\Core\Metadata\Element\Entity\ElementEntity;
use Chamilo\Core\Metadata\Element\Form\ElementForm;
use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the controlled vocabulary
 *
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $element_id = Request :: get(self :: PARAM_ELEMENT_ID);
        $element = DataManager :: retrieve_by_id(Element :: class_name(), $element_id);

        $form = new ElementForm($this->get_url(), $element);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $element->set_schema_id($values[Element :: PROPERTY_SCHEMA_ID]);
                $element->set_name($values[Element :: PROPERTY_NAME]);
                $element->set_display_name($values[Element :: PROPERTY_DISPLAY_NAME]);
                $success = $element->update();

                if ($values[self :: PROPERTY_ASSOCIATIONS])
                {
                    // Process element nestings
                    $existing_condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            ElementNesting :: class_name(),
                            ElementNesting :: PROPERTY_PARENT_ELEMENT_ID),
                        new StaticConditionVariable($element->get_id()));

                    if (DataManager :: count(
                        ElementNesting :: class_name(),
                        new DataClassCountParameters($existing_condition)) > 0)
                    {
                        $selected_entities = (array) $values[self :: PROPERTY_ASSOCIATIONS][ElementEntity :: ENTITY_TYPE];

                        // Get the currently attached elements
                        $existing_entities = array();
                        $existing_element_nestings = DataManager :: retrieves(
                            ElementNesting :: class_name(),
                            new DataClassRetrievesParameters($existing_condition));

                        while ($existing_element_nesting = $existing_element_nestings->next_result())
                        {
                            $existing_entities[] = $existing_element_nesting->get_child_element_id();
                        }

                        // Compare both sets of elements
                        $to_add = array_diff($selected_entities, $existing_entities);
                        $to_delete = array_diff($existing_entities, $selected_entities);

                        // Add the new element nestings
                        foreach ($to_add as $new_child_element_id)
                        {
                            try
                            {
                                $element_nesting = new ElementNesting();
                                $element_nesting->set_parent_element_id($element->get_id());
                                $element_nesting->set_child_element_id($new_child_element_id);
                                $success = $element_nesting->create();
                            }
                            catch (\Exception $ex)
                            {
                                $success = false;
                                $message = $ex->getMessage();
                            }
                        }

                        // Delete the outdated element nestings
                        $conditions = array();
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                ElementNesting :: class_name(),
                                ElementNesting :: PROPERTY_PARENT_ELEMENT_ID),
                            new StaticConditionVariable($element->get_id()));
                        $conditions[] = new InCondition(
                            new PropertyConditionVariable(
                                ElementNesting :: class_name(),
                                ElementNesting :: PROPERTY_CHILD_ELEMENT_ID),
                            $to_delete);

                        $element_nestings_to_delete = DataManager :: retrieves(
                            ElementNesting :: class_name(),
                            new DataClassRetrievesParameters(new AndCondition($conditions)));

                        while ($element_nesting_to_delete = $element_nestings_to_delete->next_result())
                        {
                            try
                            {
                                $success = $element_nesting_to_delete->delete();
                            }
                            catch (\Exception $ex)
                            {
                                $success = false;
                                $message = $ex->getMessage();
                            }
                        }
                    }
                    else
                    {
                        foreach ($values[self :: PROPERTY_ASSOCIATIONS][ElementEntity :: ENTITY_TYPE] as $element_id)
                        {
                            try
                            {
                                $element_nesting = new ElementNesting();
                                $element_nesting->set_parent_element_id($element->get_id());
                                $element_nesting->set_child_element_id($element_id);
                                $success = $element_nesting->create();
                            }
                            catch (\Exception $ex)
                            {
                                $success = false;
                                $message = $ex->getMessage();
                            }
                        }
                    }

                    // Process element-attribute relations
                    $existing_condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            ElementRelAttribute :: class_name(),
                            ElementRelAttribute :: PROPERTY_ELEMENT_ID),
                        new StaticConditionVariable($element->get_id()));

                    if (DataManager :: count(
                        ElementRelAttribute :: class_name(),
                        new DataClassCountParameters($existing_condition)) > 0)
                    {
                        $selected_entities = (array) $values[self :: PROPERTY_ASSOCIATIONS][AttributeEntity :: ENTITY_TYPE];

                        // Get the currently attached elements
                        $existing_entities = array();
                        $existing_element_rel_attributes = DataManager :: retrieves(
                            ElementRelAttribute :: class_name(),
                            new DataClassRetrievesParameters($existing_condition));

                        while ($existing_element_rel_attribute = $existing_element_rel_attributes->next_result())
                        {
                            $existing_entities[] = $existing_element_rel_attribute->get_attribute_id();
                        }

                        // Compare both sets of elements
                        $to_add = array_diff($selected_entities, $existing_entities);
                        $to_delete = array_diff($existing_entities, $selected_entities);

                        // Add the new element nestings
                        foreach ($to_add as $new_attribute_id)
                        {
                            try
                            {
                                $element_rel_attribute = new ElementRelAttribute();
                                $element_rel_attribute->set_element_id($element->get_id());
                                $element_rel_attribute->set_attribute_id($new_attribute_id);
                                $success = $element_rel_attribute->create();
                            }
                            catch (\Exception $ex)
                            {
                                $success = false;
                                $message = $ex->getMessage();
                            }
                        }

                        // Delete the outdated element nestings
                        $conditions = array();
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                ElementRelAttribute :: class_name(),
                                ElementRelAttribute :: PROPERTY_ELEMENT_ID),
                            new StaticConditionVariable($element->get_id()));
                        $conditions[] = new InCondition(
                            new PropertyConditionVariable(
                                ElementRelAttribute :: class_name(),
                                ElementRelAttribute :: PROPERTY_ATTRIBUTE_ID),
                            $to_delete);

                        $element_rel_attributes_to_delete = DataManager :: retrieves(
                            ElementRelAttribute :: class_name(),
                            new DataClassRetrievesParameters(new AndCondition($conditions)));

                        while ($element_rel_attribute_to_delete = $element_rel_attributes_to_delete->next_result())
                        {
                            try
                            {
                                $success = $element_rel_attribute_to_delete->delete();
                            }
                            catch (\Exception $ex)
                            {
                                $success = false;
                                $message = $ex->getMessage();
                            }
                        }
                    }
                    else
                    {
                        foreach ($values[self :: PROPERTY_ASSOCIATIONS][AttributeEntity :: ENTITY_TYPE] as $attribute_id)
                        {
                            try
                            {
                                $element_rel_attribute = new ElementRelAttribute();
                                $element_rel_attribute->set_element_id($element->get_id());
                                $element_rel_attribute->set_attribute_id($attribute_id);
                                $success = $element_rel_attribute->create();
                            }
                            catch (\Exception $ex)
                            {
                                $success = false;
                                $message = $ex->getMessage();
                            }
                        }
                    }
                }
                else
                {
                    DataManager :: delete_element_nestings_from_element($element);
                    DataManager :: delete_element_rel_attributes_from_element($element);
                }

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('Element')),
                    Utilities :: COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Adds additional breadcrumbs
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumb_trail
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE),
                    array(self :: PARAM_ELEMENT_ID)),
                Translation :: get('BrowserComponent')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ELEMENT_ID);
    }
}