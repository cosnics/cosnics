<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

class XmlSearchFeedComponent extends Manager
{

    public function run()
    {
        $conditions = [];

        $query_condition = $this->getSearchQueryConditionGenerator()->getSearchConditions(
            $this->getRequest()->request->get('queryString'),
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );

        if (isset($query_condition))
        {
            $conditions[] = $query_condition;
        }

        $owner_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID))
        );
        $conditions[] = $owner_condition;

        $category_type_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable('category')
        );
        $conditions[] = new NotCondition($category_type_condition);

        $condition = new AndCondition($conditions);

        $objects = DataManager::retrieve_active_content_objects(
            ContentObject::class, new StorageParameters(condition: $condition)
        );

        foreach ($objects as $lo)
        {
            echo '<li onclick="fill(\'' . $lo->get_title() . '\');">';
            echo $lo->get_title();
            echo '</li>';
        }
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }
}
