<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\MetadataOld\Value\MetadataValueEditorComponent;
use Chamilo\Core\MetadataOld\Value\ValueCreator;
use Chamilo\Core\User\Integration\Chamilo\Core\MetadataOld\MetadataValueCreator;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Manages the metadata for a given user
 *
 * @package user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataManagerComponent extends Manager implements DelegateComponent, MetadataValueEditorComponent
{

    /**
     * The selected user
     *
     * @var User
     */
    private $selected_user;

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
            \Chamilo\Core\MetadataOld\Value\Manager :: context(),
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
        return \Chamilo\Core\User\Integration\Chamilo\Core\MetadataOld\Storage\DataManager :: get_element_and_attribute_values_for_user(
            $this->get_selected_user()->get_id());
    }

    /**
     * Truncates the metadata values
     */
    public function truncate_values()
    {
        \Chamilo\Core\User\Integration\Chamilo\Core\MetadataOld\Storage\DataManager :: truncate_metadata_values_for_user(
            $this->get_selected_user()->get_id());
    }

    /**
     * Returns the currently selected user
     *
     * @throws ObjectNotExistException
     * @throws NoObjectSelectedException
     *
     * @return User
     */
    public function get_selected_user()
    {
        if (! isset($this->selected_user))
        {
            $user_id = $this->getRequest()->query->get(self :: PARAM_USER_USER_ID);
            if (! $user_id)
            {
                throw new NoObjectSelectedException(Translation :: get('Group'));
            }
            $user = DataManager :: retrieve_by_id(User :: class_name(), $user_id);
            if (! $user)
            {
                throw new ObjectNotExistException(Translation :: get('Group', $user_id));
            }

            $this->selected_user = $user;
        }

        return $this->selected_user;
    }
}
