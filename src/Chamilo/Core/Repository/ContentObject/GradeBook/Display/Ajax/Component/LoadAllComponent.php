<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
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

            //var_dump($gradebookData->getGradeBookColumns()[0]->getSubItems());
            //$gradeColumns = $this->getGradeBookColumnsJSON($gradebookData);
            //var_dump($this->serialize($gradeColumns));

            $gradeColumns = [
                [ 'id' => 'gr1', 'type' => 'group', 'title' => 'Groepsscore', 'subItemIds' => [1, 3], 'weight' => null, 'countForEndResult' => true, 'authPresenceEndResult' => 0, 'unauthPresenceEndResult' => 2 ],
                [ 'id' => 2, 'type' => 'item', 'title' => null, 'weight' => null, 'countForEndResult' => true, 'authPresenceEndResult' => 0, 'unauthPresenceEndResult' => 2 ],
                [ 'id' => 4, 'type' => 'item', 'title' => null, 'weight' => null, 'countForEndResult' => true, 'authPresenceEndResult' => 0, 'unauthPresenceEndResult' => 2 ],
                [ 'id' => 5, 'type' => 'item', 'title' => null, 'weight' => null, 'countForEndResult' => true, 'authPresenceEndResult' => 0, 'unauthPresenceEndResult' => 2 ],
                [ 'id' => 6, 'type' => 'item', 'title' => 'Mondeling examen', 'weight' => null, 'countForEndResult' => true, 'authPresenceEndResult' => 0, 'unauthPresenceEndResult' => 2 ]
            ];

            $categories = [
                [ 'id' => 1, 'color' => '#caf1eb', 'title' => 'Categorie 1', 'columnIds' => ['gr1', 2, 4] ],
                [ 'id' => 2, 'color' => '#ebf2e8', 'title' => 'Categorie 2', 'columnIds' => [5, 6] ]
            ];

            $nullCategory = [ 'id' => 0, 'color' => 'none', 'title' => '', 'columnIds' => [] ];

            $resultsData = [
                [ 'id' => 1, 'student' => 'Student 1', 'results' => [1 => null, 3 => 20, 2 => 60, 4 => 80, 5 => 50, 6 => 75, 7 => 50] ],
                [ 'id' => 2, 'student' => 'Student 2', 'results' => [1 => 30, 3 => null, 2 => 50, 4 => 40, 5 => 80, 6 => 65, 7 => 50] ],
                [ 'id' => 3, 'student' => 'Student 3', 'results' => [1 => null, 3 => 50, 2 => 30, 4 => 70, 5 => 80, 6 => 95, 7 => 50] ],
                [ 'id' => 4, 'student' => 'Student 4', 'results' => [1 => 80, 3 => null, 2 => 40, 4 => 40, 5 => 30, 6 => 75, 7 => 50] ],
                [ 'id' => 5, 'student' => 'Student 5', 'results' => [1 => null, 3 => 60, 2 => 10, 4 => 90, 5 => 40, 6 => 25, 7 => 50] ]
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
        $toJSON = function ($gradebookItem) {
            return GradeBookColumnJSONModel::fromGradeBookItem($gradebookItem);
        };

        return array_map($toJSON, $gradebookData->getGradeBookColumns()->toArray());
    }
}