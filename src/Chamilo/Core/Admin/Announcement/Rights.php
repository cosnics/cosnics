<?php
namespace Chamilo\Core\Admin\Announcement;

use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Rights extends RightsUtil
{
    // Course Rights
    const VIEW_RIGHT = '1';
    const TYPE_PUBLICATION = 1;

    private static $instance;

    /**
     *
     * @return Rights
     */
    public static function getInstance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    public static function get_available_rights($location)
    {
        return array(Translation :: get('ViewRight') => self :: VIEW_RIGHT);
    }

    public function is_allowed_in_publciation($identifier, $user_id = null)
    {
        if (is_null($user_id))
        {
            $user_id = Session :: get_user_id();
        }

        $entities = array();
        $entities[] = UserEntity :: getInstance();
        $entities[] = PlatformGroupEntity :: getInstance();

        return parent :: is_allowed(
            self :: VIEW_RIGHT,
            Manager :: context(),
            $user_id,
            $entities,
            $identifier,
            self :: TYPE_PUBLICATION);
    }

    public function render_target_entities_as_string($entities)
    {
        $rdm = \Chamilo\Core\Rights\Storage\DataManager :: getInstance();

        $target_list = array();

        // don't display each individual user if it is published for
        // everybody...
        // if a name is alfabetically before "everybody" this would be the
        // selected
        // item in the dropdownlist which works confusing when you expect
        // "everybody"
        if (array_key_exists(0, $entities[0]))
        {
            $target_list[] = Translation :: get('Everybody', null, Utilities :: COMMON_LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';

            foreach ($entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case PlatformGroupEntity :: ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                                \Chamilo\Core\Group\Storage\DataClass\Group :: class_name(),
                                $group_id);
                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case UserEntity :: ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $target_list[] = '<option>' .
                                 \Chamilo\Core\User\Storage\DataManager :: get_fullname_from_user($user_id) . '</option>';
                        }
                        break;
                    case 0 :
                        $target_list[] = '<option>' .
                             Translation :: get('Everybody', null, Utilities :: COMMON_LIBRARIES) . '</option>';
                        break;
                }
            }

            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }
}
