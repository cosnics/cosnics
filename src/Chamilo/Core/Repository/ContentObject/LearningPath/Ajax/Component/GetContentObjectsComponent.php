<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns content objects as an array
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 * @author Sven Vanpoucke - Hogeschool Gent
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
     * @param int $categoryId
     * @param string $searchQuery
     *
     * @return array
     */
    protected function getContentObjectsArray($categoryId = null, string $searchQuery = null)
    {
        $workspace = $this->getWorkspaceFromRequest();
        $service = new ContentObjectService(new ContentObjectRepository());

        $filterData = $this->getFilterData($categoryId, $searchQuery, $workspace);
        $filterConditionRenderer = new ConditionFilterRenderer($filterData, $workspace);

        $contentObjects = $service->getContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $workspace, $filterConditionRenderer
        );

        $contentObjectsArray = [];

        foreach($contentObjects as $contentObject)
        {
            /**
             * @var ContentObject $contentObject
             */
            if (!$this->validateContentObject($contentObject))
            {
                continue;
            }

            if ($contentObject instanceof File && $contentObject->is_image())
            {
                $type = 'image';
            }
            else
            {
                $type = ClassnameUtilities::getInstance()->getClassNameFromNamespace($contentObject->get_type(), true);
            }

            array_push(
                $contentObjectsArray,
                array(
                    'id' => $contentObject->getId(),
                    'title' => $contentObject->get_title(),
                    'icon' => $contentObject->getGlyph()->render(),
                    'securityCode' => $contentObject->calculate_security_code(),
                    'type' => $type
                )
            );
        }

        return $contentObjectsArray;
    }

    /**
     * Validates the given content object
     *
     * @param ContentObject $contentObject
     *
     * @return bool
     */
    protected function validateContentObject(ContentObject $contentObject)
    {
        return ($contentObject instanceOf Includeable);
    }

    /**
     * Returns the filter data for the given category, search query and workspace
     *
     * @param int $categoryId
     * @param string $searchQuery
     * @param WorkspaceInterface $workspace
     *
     * @return FilterData
     */
    protected function getFilterData($categoryId = null, string $searchQuery, WorkspaceInterface $workspace): FilterData
    {
        $filterData = new FilterData($workspace);
        $filterData->clear(false);

        if (!is_null($categoryId) && empty($searchQuery))
        {
            $filterData->set_filter_property(FilterData::FILTER_CATEGORY, $categoryId);
        }

        $filterData->set_filter_property(FilterData::FILTER_TEXT, $searchQuery);

        return $filterData;
    }
}