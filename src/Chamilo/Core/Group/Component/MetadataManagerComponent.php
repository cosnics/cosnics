<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Integration\Chamilo\Core\MetadataOld\GroupMetadataValueCreator;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\MetadataOld\Value\MetadataValueEditorComponent;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Translation;

/**
 * Manages the metadata for a given group
 *
 * @package group
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataManagerComponent extends Manager implements DelegateComponent, MetadataValueEditorComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $factory = new ApplicationFactory(
            \Chamilo\Core\MetadataOld\Value\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    /**
     * Returns additional information for the component
     *
     * @return string
     */
    public function get_additional_information()
    {
        $group = $this->get_selected_group();

        $html = array();

        $html[] = '<h4>' . Translation :: get('GroupDetails') . '</h4>';

        $html[] = '<div class="group_details">';
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $group->get_name() . '<br />';
        $html[] = '<b>' . Translation :: get('Code') . '</b>: ' . $group->get_code() . '<br />';
        $html[] = '<b>' . Translation :: get('Users', null, \Chamilo\Core\User\Manager :: context()) . '</b>: ' .
             $group->count_users() . '<br />';
        $html[] = '<b>' . Translation :: get('Subroups') . '</b>: ' . $group->count_subgroups();
        $html[] = '</div>';
        $html[] = '<br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the additional parameters needed for this component
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_GROUP_ID);
    }

    /**
     * Redirects the user after the update
     *
     * @param bool $success
     * @param string $message
     */
    public function redirect_after_update($success, $message)
    {
        $this->redirect(
            $message,
            ! $success,
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS),
            $this->get_additional_parameters());
    }

    /**
     * Returns the value creator for the editor
     *
     * @return ValueCreator
     */
    public function get_value_creator()
    {
        return new GroupMetadataValueCreator($this->get_selected_group());
    }

    /**
     * Returns the element values
     *
     * @return ElementValue[]
     */
    public function get_element_values()
    {
        return \Chamilo\Core\Group\Integration\Chamilo\Core\MetadataOld\Storage\DataManager :: get_element_and_attribute_values_for_group(
            $this->get_selected_group()->get_id());
    }

    /**
     * Truncates the metadata values
     */
    public function truncate_values()
    {
        \Chamilo\Core\Group\Integration\Chamilo\Core\MetadataOld\Storage\DataManager :: truncate_metadata_values_for_group(
            $this->get_selected_group()->get_id());
    }
}
