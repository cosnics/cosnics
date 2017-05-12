<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class HtmlEditorFileUploadComponent
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 *
 * @todo move...
 */
class GetContentObjectsComponent extends Manager
{
    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_SEARCH_QUERY = 'search_query';

    /**
     * @inheritdoc
     */
    function run()
    {
        $categoryId = $this->getRequest()->request->get(self::PARAM_CATEGORY_ID);
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
        $response = new JsonResponse($this->getContentObjectsArray($categoryId, $searchQuery));
        $response->send();
    }

    /**
     * @param int    $categoryId
     * @param string $searchQuery
     * @return array
     */
    protected function getContentObjectsArray(int $categoryId, string $searchQuery = null)
    {

        $contentObjects = DataManager::retrieve_active_content_objects(
            ContentObject::class_name(),
            $this->getParameters($categoryId, $searchQuery));

        $contentObjectsArray = array();

        while ($contentObject = $contentObjects->next_result()) {
            if($contentObject instanceof LearningPathItem
                || $contentObject instanceof PortfolioItem
                || $contentObject instanceof LearningPath
            ) {
                continue; //@todo better way of fetching 'dragable content objects'
            }
            if ($contentObject instanceof File && $contentObject->is_image()) {
                $type = 'image';
            } else {
                $type = ClassnameUtilities::getInstance()->getClassNameFromNamespace($contentObject->get_type(), true);
            }

            array_push($contentObjectsArray,
                array(
                    'id' => $contentObject->getId(),
                    'title' => $contentObject->get_title(),
                    'icon' => $contentObject->get_icon_path(),
                    'securityCode' => $contentObject->calculate_security_code(),
                    'type' => $type
                )
            );
        }

        return $contentObjectsArray;

    }


    /**
     * @param $categoryId
     * @param $searchQuery
     * @return DataClassRetrievesParameters
     */
    protected function getParameters(int $categoryId, string $searchQuery  = null)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId()));

        if(empty($searchQuery)) {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_PARENT_ID),
                new StaticConditionVariable($categoryId)
            );
        } else {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE),
                '*' . $searchQuery . '*');
        }

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE),
                new StaticConditionVariable(ContentObject::STATE_RECYCLED)));

        return new DataClassRetrievesParameters(
            new AndCondition($conditions),
            null,
            null,
            new OrderBy(new PropertyConditionVariable(
                ContentObject::class_name(),
                ContentObject::PROPERTY_TITLE
                )
            )
        );
    }
}