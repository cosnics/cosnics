<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * A content object publication in the personal calendar application
 *
 * @package application\calendar$Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class Publication extends \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
{

    // Properties
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';

    /**
     *
     * @var int[]
     */
    private $target_groups;

    /**
     *
     * @var int[]
     */
    private $target_users;

    /**
     * Get the default properties of all Publications.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the user of this Publication object
     *
     * @return int the user
     */
    public function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Returns the published timestamp of this Publication object
     *
     * @return Timestamp the published date
     */
    public function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the user of this Publication.
     *
     * @param int $user the User.
     */
    public function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Sets the published date of this Publication.
     *
     * @param int $published the timestamp of the published date.
     */
    public function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_publication_object()
    {
        return parent::getContentObject();
    }

    /**
     *
     * @return \core\user\User
     */
    public function get_publication_publisher()
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            (int) $this->get_publisher());
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create()
    {
        $this->set_published(time());

        if (! parent :: create())
        {
            return false;
        }

        if (! $this->process_users_and_groups())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update()
    {
        if (! parent :: update())
        {
            return false;
        }

        if (! DataManager :: truncate_users_and_groups_for_publication($this->get_id()))
        {
            return false;
        }

        if (! $this->process_users_and_groups())
        {
            return false;
        }

        return true;
    }

    /**
     * Process the selected target users and groups
     *
     * @return boolean
     */
    public function process_users_and_groups()
    {
        $users = $this->get_target_users();

        foreach ($users as $index => $user_id)
        {
            $publication_user = new PublicationUser();
            $publication_user->set_publication($this->get_id());
            $publication_user->set_user($user_id);

            if (! $publication_user->create())
            {
                return false;
            }
        }

        $groups = $this->get_target_groups();

        foreach ($groups as $index => $group_id)
        {
            $publication_group = new PublicationGroup();
            $publication_group->set_publication($this->get_id());
            $publication_group->set_group_id($group_id);

            if (! $publication_group->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Create all needed for migration tool to set the published time manually
     */
    public function create_all()
    {
        return parent :: create();
    }

    /**
     *
     * @return int[]
     */
    public function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $this->target_users = DataManager :: retrieve_target_users($this);
        }

        return $this->target_users;
    }

    /**
     *
     * @return int[]
     */
    public function get_target_groups()
    {
        if (! isset($this->target_groups))
        {
            $this->target_groups = DataManager :: retrieve_target_groups($this);
        }

        return $this->target_groups;
    }

    /**
     *
     * @param int[] $target_users
     */
    public function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    /**
     *
     * @param int[] $target_groups
     */
    public function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    /**
     *
     * @return boolean
     */
    public function is_for_nobody()
    {
        return (count($this->get_target_users()) == 0 && count($this->get_target_groups()) == 0);
    }

    /**
     *
     * @param \core\user\storage\data_class\User $user
     * @return boolean
     */
    public function is_target($user)
    {
        if ($this->is_for_nobody())
        {
            return false;
        }

        $user_id = $user->get_id();

        $target_users = $this->get_target_users();
        $target_groups = $this->get_target_groups();

        $user_groups = $user->get_groups(true);

        if (in_array($user_id, $target_users))
        {
            return true;
        }
        else
        {
            foreach ($user_groups as $user_group)
            {
                if (in_array($user_group, $target_groups))
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return \libraries\storage\Condition[string]
     */
    protected function get_dependencies()
    {
        return array(
            PublicationUser :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(PublicationUser :: class_name(), PublicationUser :: PROPERTY_PUBLICATION),
                new StaticConditionVariable($this->get_id())),
            PublicationGroup :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(PublicationGroup :: class_name(), PublicationGroup :: PROPERTY_PUBLICATION),
                new StaticConditionVariable($this->get_id())));
    }
}
