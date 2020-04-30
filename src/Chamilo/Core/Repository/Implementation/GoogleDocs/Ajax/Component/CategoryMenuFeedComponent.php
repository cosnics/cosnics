<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Ajax\Component;

use Chamilo\Core\Repository\Implementation\GoogleDocs\Ajax\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class CategoryMenuFeedComponent extends Manager
{

    function run()
    {
        $groups_tree = array();

        $parent_id = Request::get('parent_id');
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parent_id)
        );

        $categories_tree = DataManager::retrieve_categories(
            $condition, null, null, new OrderBy(
                new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_NAME)
            )
        );
    }

    public function dump_tree($categories)
    {
        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        while ($category = $categories->next_result())
        {
            $has_children = $category->has_children() ? 1 : 0;

            echo '<leaf id="' . $category->get_id() . '" classes="' . $glyph->getClassNamesString() .
                '" has_children="' . $has_children . '" title="' . htmlspecialchars(
                    $category->get_name()
                ) . '" description="' . htmlspecialchars($category->get_name()) . '"/>' . PHP_EOL;
        }
    }
}