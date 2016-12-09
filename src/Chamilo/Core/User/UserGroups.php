<?php
namespace Chamilo\Core\User;

use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
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
     * Constructor
     * 
     * @param $user_id int
     * @param $border boolean Indicates if a border should be included
     */
    public function __construct($user_id, $border = true)
    {
        $this->user_id = $user_id;
        $this->border = $border;
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
}
