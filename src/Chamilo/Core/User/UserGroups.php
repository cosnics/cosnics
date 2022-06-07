<?php
namespace Chamilo\Core\User;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class UserGroups
{

    /**
     * Limit the shown groups to the given list of available groups in a certain context
     *
     * @var Group[]
     */
    protected $availableGroups;

    /**
     * The user ID
     */
    private $user_id;

    /**
     * Indicates if a border should be included
     */
    private $border;

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

        foreach ($availableGroups as $availableGroup)
        {
            $this->availableGroups[$availableGroup->getId()] = $availableGroup;
        }
    }

    protected function isGroupValid(Group $group)
    {
        if (!is_array($this->availableGroups) || count($this->availableGroups) == 0)
        {
            return false;
        }

        return array_key_exists($group->getId(), $this->availableGroups);
    }

    /**
     * Returns a HTML representation of the user details
     *
     * @return string
     * @todo Implement further details
     */
    public function toHtml()
    {
        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');
        $html[] = $glyph->render() . '&nbsp;' . Translation::get('PlatformGroups', null, StringUtilities::LIBRARIES);

        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $group_relations = DataManager::retrieve_user_groups($this->user_id);

        if ($group_relations->count() > 0)
        {
            $groupElements = [];

            foreach($group_relations as $group)
            {
                if (!$this->isGroupValid($group))
                {
                    continue;
                }

                $groupElements[] = '<li>';
                $groupElements[] = $group->get_name();
                $groupElements[] = ' (';
                $groupElements[] = $group->get_code();
                $groupElements[] = ')';
                $groupElements[] = '</li>';
            }

            if (count($groupElements) > 0)
            {
                $html[] = '<ul>';
                $html[] = implode(PHP_EOL, $groupElements);
                $html[] = '</ul>';
            }
            else
            {
                $html[] = Translation::get('NoPlatformGroupSubscriptions', null, StringUtilities::LIBRARIES);
            }
        }
        else
        {
            $html[] = Translation::get('NoPlatformGroupSubscriptions', null, StringUtilities::LIBRARIES);
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
