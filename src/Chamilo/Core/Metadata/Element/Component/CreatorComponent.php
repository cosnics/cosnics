<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Attribute\Entity\AttributeEntity;
use Chamilo\Core\Metadata\Element\Entity\ElementEntity;
use Chamilo\Core\Metadata\Element\Form\ElementForm;
use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to create the schema
 */
class CreatorComponent extends Manager
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

        $form = new ElementForm($this->get_url());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $element = new Element();
                $element->set_schema_id($values[Element :: PROPERTY_SCHEMA_ID]);
                $element->set_name($values[Element :: PROPERTY_NAME]);
                $element->set_display_name($values[Element :: PROPERTY_DISPLAY_NAME]);
                $success = $element->create();

                if ($values[self :: PROPERTY_ASSOCIATIONS])
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

                    foreach ($values[self :: PROPERTY_ASSOCIATIONS][AttributeEntity :: ENTITY_TYPE] as $attribute_id)
                    {
                        try
                        {
                            $element_rel_attribute = new ElementRelAttribute();
                            $element_rel_attribute->set_attribute_id($attribute_id);
                            $element_rel_attribute->set_element_id($element->get_id());
                            $success = $element_rel_attribute->create();
                        }
                        catch (\Exception $ex)
                        {
                            $success = false;
                            $message = $ex->getMessage();
                        }
                    }
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
                $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent')));
    }
}