<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Form\RightsForm;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Rights configuration of portfolio items and/or folders
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsComponent extends ItemComponent
{

    /**
     * Executes this component
     */
    public function build()
    {
        BreadcrumbTrail :: getInstance()->add(new Breadcrumb($this->get_url(), Translation :: get('RightsComponent')));

        if ($this->get_parent() instanceof PortfolioComplexRights &&
             $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $available_nodes = $this->get_available_nodes();

            if (count($available_nodes) == 0)
            {
                if ($this->get_current_node()->is_root())
                {
                    $target_step = $this->get_current_node()->get_id();
                }
                else
                {
                    $target_step = $this->get_current_node()->get_parent()->get_id();
                }

                $parameters = array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    self :: PARAM_STEP => $target_step);

                $this->redirect(
                    Translation :: get(
                        'NoObjectsToConfigureRightsFor',
                        array('OBJECTS' => Translation :: get('ComplexContentObjectItems')),
                        Utilities :: COMMON_LIBRARIES),
                    true,
                    $parameters);
            }

            $locations = $this->get_parent()->get_locations($available_nodes);

            $form = new RightsForm(
                $this->get_url(),
                $locations,
                $this->get_parent()->get_available_rights(),
                $this->get_parent()->get_entities(),
                $this->get_parent()->get_selected_entities($this->get_current_node()));

            if ($form->validate())
            {
                $succes = $this->handle_rights($locations, $form->exportValues());

                $message = Translation :: get($succes ? 'RightsChanged' : 'RightsNotChanged');
                $this->redirect($message, ! $succes);
            }

            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $message = Display :: error_message(Translation :: get('ComplexRightsNotSupported'), true);

            $html = array();

            $html[] = $this->render_header();
            $html[] = $message;
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Get the list of actual available nodes, based o the set of requested nodes
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public function get_available_nodes()
    {
        $selected_steps = $this->getRequest()->get(self :: PARAM_STEP);
        if (! is_array($selected_steps))
        {
            $selected_steps = array($selected_steps);
        }

        $path = $this->get_root_content_object()->get_complex_content_object_path();

        $available_nodes = array();

        foreach ($selected_steps as $selected_step)
        {
            $selected_node = $path->get_node($selected_step);

            if ($this->get_parent()->is_allowed_to_set_content_object_rights())
            {
                $available_nodes[] = $selected_node;
            }
        }

        return $available_nodes;
    }

    /**
     * Handle the submitted rights for the given locations
     *
     * @param string[] $values
     * @return boolean
     */
    public function handle_rights($locations, $values)
    {
        $succes = true;

        foreach ($locations as $location)
        {

            if (! $location->clear_rights())
            {
                $succes = false;
                continue;
            }

            if ($values[RightsForm :: PROPERTY_INHERIT] == RightsForm :: INHERIT_TRUE)
            {
                if (! $location->inherits())
                {
                    $location->inherit();
                    $succes &= $location->update();
                }
            }
            else
            {
                if ($location->inherits())
                {
                    $location->disinherit();
                    $succes &= $location->update();
                }

                $succes &= $this->handle_location_rights($values, $location);
            }
        }

        return $succes;
    }

    /**
     * Handles the rights options for the specific location
     *
     * @param string[] $values
     */
    private function handle_location_rights($values, $location)
    {
        $location_id = $location->get_node_id();

        $succes = true;

        foreach ($this->get_parent()->get_available_rights() as $right_id)
        {
            $option = $values[RightsForm :: PROPERTY_RIGHT_OPTION . '_' . $right_id];

            switch ($option)
            {
                case RightsForm :: RIGHT_OPTION_ALL :
                    $succes &= $this->get_parent()->invert_location_entity_right($right_id, 0, 0, $location_id);
                    break;
                case RightsForm :: RIGHT_OPTION_ME :
                    $succes &= $this->get_parent()->invert_location_entity_right(
                        $right_id,
                        $this->get_user_id(),
                        1,
                        $location_id);
                    break;
                case RightsForm :: RIGHT_OPTION_SELECT :
                    foreach ($values[RightsForm :: PROPERTY_TARGETS . '_' . $right_id] as $entity_type => $target_ids)
                    {
                        foreach ($target_ids as $target_id)
                        {
                            $succes &= $this->get_parent()->invert_location_entity_right(
                                $right_id,
                                $target_id,
                                $entity_type,
                                $location_id);
                        }
                    }
            }
        }

        return $succes;
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP);
    }
}
