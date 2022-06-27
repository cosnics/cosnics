<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadAllComponent extends Manager
{
    function run()
    {
        try
        {
            $gradebook = $this->getGradeBook();
            $gradebookData = $this->getGradeBookService()->getGradeBook($gradebook->getActiveGradeBookDataId(), null);
            $gradeItems = $this->getGradeBookItemsJSON($gradebookData);
            $gradeColumns = $this->getGradeBookColumnsJSON($gradebookData);
            $categories = $this->getGradeBookCategoriesJSON($gradebookData);

            $nullCategory = [ 'id' => 0, 'color' => 'none', 'title' => '', 'columnIds' => [] ];

            $resultsData = [
                [ 'id' => 1, 'student' => 'Student 1', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 20  ], ['id' => 2, 'value' => 60], ['id' => 4, 'value' => 80], ['id' => 5, 'value' => 50], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
                [ 'id' => 2, 'student' => 'Student 2', 'results' => [['id' => 1, 'value' => 30  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 50], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 65], ['id' => 7, 'value' => 50]] ],
                [ 'id' => 3, 'student' => 'Student 3', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 50  ], ['id' => 2, 'value' => 30], ['id' => 4, 'value' => 70], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 95], ['id' => 7, 'value' => 50]] ],
                [ 'id' => 4, 'student' => 'Student 4', 'results' => [['id' => 1, 'value' => 80  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 40], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 30], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
                [ 'id' => 5, 'student' => 'Student 5', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 60  ], ['id' => 2, 'value' => 10], ['id' => 4, 'value' => 90], ['id' => 5, 'value' => 40], ['id' => 6, 'value' => 25], ['id' => 7, 'value' => 50]] ]
            ];

            $gradeBookObject = ['gradeItems' => $gradeItems, 'gradeColumns' => $gradeColumns, 'categories' => $categories, 'nullCategory' => $nullCategory, 'resultsData' => $resultsData];

            return new JsonResponse($this->serialize($gradeBookObject), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return GradeBookItemJSONModel[]
     */
    public function getGradeBookItemsJSON(GradeBookData $gradebookData): array
    {
        $toJSON = function ($gradebookItem) {
            return GradeBookItemJSONModel::fromGradeBookItem($gradebookItem);
        };

        return array_map($toJSON, $gradebookData->getGradeBookItems()->toArray());
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return GradeBookColumnJSONModel[]
     */
    public function getGradeBookColumnsJSON(GradeBookData $gradebookData): array
    {
        $toJSON = function ($gradebookColumn) {
            return GradeBookColumnJSONModel::fromGradeBookColumn($gradebookColumn);
        };

        return array_map($toJSON, $gradebookData->getGradeBookColumns()->toArray());
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return GradeBookCategoryJSONModel[]
     */
    public function getGradeBookCategoriesJSON(GradeBookData $gradebookData): array
    {
        $toJSON = function ($gradebookCategory) {
            return GradeBookCategoryJSONModel::fromGradeBookCategory($gradebookCategory);
        };

        return array_map($toJSON, $gradebookData->getGradeBookCategories()->toArray());
    }
}