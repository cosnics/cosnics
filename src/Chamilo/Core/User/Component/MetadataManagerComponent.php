<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Metadata\Value\MetadataValueEditorComponent;
use Chamilo\Core\Metadata\Value\ValueCreator;
use Chamilo\Core\User\Integration\Chamilo\Core\Metadata\MetadataValueCreator;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Manages the metadata for a given user
 *
 * @package user
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
            $this->getRequest(),
            \Chamilo\Core\Metadata\Value\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    /**
     * Returns the additional parameters needed for this component
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_USER_USER_ID);
    }

    /**
     * Adds additional breadcrumbs to the breadcrumb trail
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $selected_user = $this->get_selected_user();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_USER_DETAIL,
                        self :: PARAM_USER_USER_ID => $selected_user->get_id())),
                $selected_user->get_fullname()));
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
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE_USERS),
            $this->get_additional_parameters());
    }

    /**
     * Returns the value creator for the editor
     *
     * @return ValueCreator
     */
    public function get_value_creator()
    {
        return new MetadataValueCreator($this->get_selected_user());
    }

    /**
     * Returns the element values
     *
     * @return ElementValue[]
     */
    public function get_element_values()
    {
        return \Chamilo\Core\User\Integration\Chamilo\Core\Metadata\Storage\DataManager :: get_element_and_attribute_values_for_user(
            $this->get_selected_user()->get_id());
    }

    /**
     * Truncates the metadata values
     */
    public function truncate_values()
    {
        \Chamilo\Core\User\Integration\Chamilo\Core\Metadata\Storage\DataManager :: truncate_metadata_values_for_user(
            $this->get_selected_user()->get_id());
    }
}
