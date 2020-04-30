<?php
namespace Chamilo\Core\Group\Ajax\Component;

use Chamilo\Core\Group\Ajax\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Group\XmlFeeds
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlGroupMenuFeedComponent extends Manager
{

    public function run()
    {
        $groups_tree = array();

        $parent_id = Request::get('parent_id');
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_id)
        );
        $groups_tree = DataManager::retrieves(
            Group::class, new DataClassRetrievesParameters(
                $condition, null, null,
                new OrderBy(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))
            )
        )->as_array();

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL, '<tree>' . PHP_EOL;
        echo $this->dump_tree($groups_tree);
        echo '</tree>';
    }

    public function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }

        return false;
    }

    public function dump_groups_tree($groups)
    {
        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        foreach ($groups as $group)
        {
            $description = strip_tags($group->get_fully_qualified_name() . ' [' . $group->get_code() . ']');

            $has_children = $group->has_children() ? 1 : 0;
            echo '<leaf id="' . $group->get_id() . '" classes="' . $glyph->getClassNamesString() . '" has_children="' .
                $has_children . '" title="' . htmlspecialchars($group->get_name()) . '" description="' .
                htmlspecialchars($description) . '"/>' . PHP_EOL;
        }
    }

    public function dump_tree($groups)
    {
        $html = array();

        if ($this->contains_results($groups))
        {
            $this->dump_groups_tree($groups);
        }
    }
}