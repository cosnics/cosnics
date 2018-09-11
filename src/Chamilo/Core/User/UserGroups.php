<?php
namespace Chamilo\Core\User;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class UserGroups
{

    /**
     * The user ID
     */
    private $user_id;

    /**
     * Indicates if a border should be included
     */
    private $border;

    /**
     * Limit the shown groups to the given list of available groups in a certain context
     *
     * @var Group[]
     */
    protected $availableGroups;

    /**
     * Constructor
     * 
     * @param $user_id int
     * @param $border boolean Indicates if a border should be included
     */
    public function __construct($user_id, $border = true, $availableGroups = [])
    {
        $this->user_id = $user_id;
        $this->border = $border;

        foreach($availableGroups as $availableGroup)
        {
            $this->availableGroups[$availableGroup->getId()] = $availableGroup;
        }
    }

    /**
     * Returns a HTML representation of the user details
     * 
     * @return string
     * @todo Implement further details
     */
    public function toHtml()
    {
        $html[] = '<div ';
        if ($this->border)
        {
            $html[] = 'class="user_details"';
        }
        else
        {
            $html[] = 'class="vertical_space"';
        }
        $html[] = 'style="clear: both;background-image: url(' . Theme::getInstance()->getImagePath(
            \Chamilo\Core\Group\Manager::context(), 
            'Logo/22') . ');">';
        $html[] = '<div class="title">';
        $html[] = Translation::get('PlatformGroups', null, Utilities::COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul>';
        $group_relations = \Chamilo\Core\Group\Storage\DataManager::retrieve_user_groups($this->user_id);
        if ($group_relations->size() > 0)
        {
            while ($group = $group_relations->next_result())
            {
                if(!$this->isGroupValid($group))
                {
                    continue;
                }

                $html[] = '<li>';
                $html[] = $group->get_name();
                $html[] = ' (';
                $html[] = $group->get_code();
                $html[] = ')';
                $html[] = '</li>';
            }
        }
        else
        {
            $html[] = Translation::get('NoPlatformGroupSubscriptions', null, Utilities::COMMON_LIBRARIES);
        }
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '<div style="clear:both;"><span></span></div>';
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    protected function isGroupValid(Group $group)
    {
        if(!is_array($this->availableGroups) || count($this->availableGroups) == 0)
        {
            return false;
        }

        return array_key_exists($group->getId(), $this->availableGroups);
    }
}
