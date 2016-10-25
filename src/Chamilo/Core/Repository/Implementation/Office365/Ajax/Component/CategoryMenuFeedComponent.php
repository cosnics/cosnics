<?php
namespace Chamilo\Core\Repository\Implementation\Office365\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class CategoryMenuFeedComponent extends \Chamilo\Core\Repository\Implementation\Office365\Ajax\Manager
{

    function run()
    {
        $groups_tree = array();

        $parent_id = Request :: get('parent_id');
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_PARENT),
            new StaticConditionVariable($parent_id));
        $categories_tree = DataManager :: retrieve_categories(
            $condition,
            null,
            null,
            new OrderBy(
                new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_NAME)));

    /**
     * // header('Content-Type: text/xml');
     * // echo '<?xml version="1.0" encoding="UTF-8"?>' .
     * "\n", '<tree>' . "\n";
     * // echo $this->dump_tree($categories_tree);
     * // echo '</tree>';
     */
    }

    public function dump_tree($categories)
    {
        while ($category = $categories->next_result())
        {
            $has_children = $category->has_children() ? 1 : 0;
            echo '<leaf id="' . $category->get_id() . '" classes="category" has_children="' . $has_children . '" title="' . htmlspecialchars(
                $category->get_name()) . '" description="' . htmlspecialchars($category->get_name()) . '"/>' . "\n";
        }
    }
}