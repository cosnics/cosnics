<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

/*use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\RubricHasResultsException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\Level;*/

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
//use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository\GradeBookDataRepository;

/**
 * Class GradeBookService
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Service
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookService
{
    /**
     * @var GradeBookDataRepository
     */
    protected $gradeBookDataRepository;

    /*/**
     * @var RubricValidator
     */
    //protected $rubricValidator;

    /*/**
     * @var RubricTreeBuilder
     */
    //protected $rubricTreeBuilder;

    /*/**
     * @var RubricResultService
     */
    //protected $rubricResultService;

    /**
     * GradeBookService constructor.
     *
     * @param GradeBookDataRepository $gradeBookDataRepository
     */
    public function __construct(GradeBookDataRepository $gradeBookDataRepository)
    {
        $this->gradeBookDataRepository = $gradeBookDataRepository;
    }

    /**
     * Retrieves a gradebook from the database
     *
     * @param int $gradeBookDataId
     * @param int|null $expectedVersion
     *
     * @return GradeBookData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function getGradeBook(int $gradeBookDataId, int $expectedVersion = null)
    {
        return $this->gradeBookDataRepository->findEntireGradeBookById($gradeBookDataId, $expectedVersion);
    }

    /**
     * @param GradeBookData $gradeBookData
     *
     */
    public function saveGradeBook(GradeBookData $gradeBookData)
    {
        /*if(!$this->canChangeRubric($gradeBookData))
        {
            throw new RubricHasResultsException();
        }*/

        $gradeBookData->setLastUpdated(new \DateTime());
        //$this->rubricValidator->validateRubric($rubricData);

        $this->gradeBookDataRepository->saveGradeBookData($gradeBookData);
    }

    /**
     * @param int $gradeBookDataId
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteGradeBookData(int $gradeBookDataId)
    {
        $gradeBookData = $this->getGradeBook($gradeBookDataId);
        $this->gradeBookDataRepository->deleteGradeBookData($gradeBookData);
    }

    /**
     * @param GradeBook $gradebook
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getGradeBookObjectData(GradeBook $gradebook): array
    {
        $gradebookData = $this->getGradeBook($gradebook->getActiveGradeBookDataId(), null);

        $resultsData = [
            [ 'id' => 1, 'student' => 'Student 1', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 20  ], ['id' => 2, 'value' => 60], ['id' => 4, 'value' => 80], ['id' => 5, 'value' => 50], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 2, 'student' => 'Student 2', 'results' => [['id' => 1, 'value' => 30  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 50], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 65], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 3, 'student' => 'Student 3', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 50  ], ['id' => 2, 'value' => 30], ['id' => 4, 'value' => 70], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 95], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 4, 'student' => 'Student 4', 'results' => [['id' => 1, 'value' => 80  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 40], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 30], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 5, 'student' => 'Student 5', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 60  ], ['id' => 2, 'value' => 10], ['id' => 4, 'value' => 90], ['id' => 5, 'value' => 40], ['id' => 6, 'value' => 25], ['id' => 7, 'value' => 50]] ]
        ];

        return [
            'gradeItems' => $this->getGradeBookItemsJSON($gradebookData),
            'gradeColumns' => $this->getGradeBookColumnsJSON($gradebookData),
            'categories' => $this->getGradeBookCategoriesJSON($gradebookData),
            'nullCategory' => [ 'id' => 0, 'color' => 'none', 'title' => '', 'columnIds' => [] ],
            'resultsData' => $resultsData
        ];
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return GradeBookItemJSONModel[]
     */
    private function getGradeBookItemsJSON(GradeBookData $gradebookData): array
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
    private function getGradeBookColumnsJSON(GradeBookData $gradebookData): array
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
    private function getGradeBookCategoriesJSON(GradeBookData $gradebookData): array
    {
        $toJSON = function ($gradebookCategory) {
            return GradeBookCategoryJSONModel::fromGradeBookCategory($gradebookCategory);
        };

        return array_map($toJSON, $gradebookData->getGradeBookCategories()->toArray());
    }
}
