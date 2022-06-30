<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use JMS\Serializer\Serializer;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookAjaxService
{
    /**
     * @var GradeBookService
     */
    protected $gradeBookService;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * GradeBookAjaxService constructor.
     *
     * @param GradeBookService $gradeBookService
     * @param Serializer $serializer
     */
    public function __construct(GradeBookService $gradeBookService, Serializer $serializer)
    {
        $this->gradeBookService = $gradeBookService;
        $this->serializer = $serializer;
    }

    /**
     * @param GradeBook $gradebook
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getGradeBookObjectData(GradeBook $gradebook): array
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradebook->getActiveGradeBookDataId(), null);

        $resultsData = [
            [ 'id' => 1, 'student' => 'Student 1', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 20  ], ['id' => 2, 'value' => 60], ['id' => 4, 'value' => 80], ['id' => 5, 'value' => 50], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 2, 'student' => 'Student 2', 'results' => [['id' => 1, 'value' => 30  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 50], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 65], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 3, 'student' => 'Student 3', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 50  ], ['id' => 2, 'value' => 30], ['id' => 4, 'value' => 70], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 95], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 4, 'student' => 'Student 4', 'results' => [['id' => 1, 'value' => 80  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 40], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 30], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 5, 'student' => 'Student 5', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 60  ], ['id' => 2, 'value' => 10], ['id' => 4, 'value' => 90], ['id' => 5, 'value' => 40], ['id' => 6, 'value' => 25], ['id' => 7, 'value' => 50]] ]
        ];

        return [
            'dataId' => $gradebookData->getId(),
            'version' => $gradebookData->getVersion(),
            'title' => $gradebookData->getTitle(),
            'gradeItems' => $this->getGradeBookItemsJSON($gradebookData),
            'gradeColumns' => $this->getGradeBookColumnsJSON($gradebookData),
            'categories' => $this->getGradeBookCategoriesJSON($gradebookData),
            'nullCategory' => new GradeBookCategoryJSONModel(0, '', 'none', $gradebookData->getGradeBookColumnsUncategorized()),
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

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $category = $gradebookCategoryJSONModel->toGradeBookCategory($gradebookData);
        $gradebookData->addGradeBookCategory($category);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'category' => $gradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $category = $gradebookData->getGradeBookCategoryById($gradebookCategoryJSONModel->getId());
        $gradebookCategoryJSONModel->updateGradeBookCategory($category);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'category' => $gradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData, int $newSort)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $category = $gradebookData->getGradeBookCategoryById($gradebookCategoryJSONModel->getId());
        $gradebookData->moveGradeBookCategory($category, $newSort);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'category' => $gradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param string $gradeBookCategoryJSONData
     *
     * @return GradeBookCategoryJSONModel
     */
    protected function parseGradeBookCategoryJSONModel(string $gradeBookCategoryJSONData)
    {
        $gradeBookJSONModel = $this->serializer->deserialize(
            $gradeBookCategoryJSONData, GradeBookCategoryJSONModel::class, 'json'
        );
        if (!$gradeBookJSONModel instanceof GradeBookCategoryJSONModel)
        {
            throw new \RuntimeException('Could not parse the gradebook category JSON model');
        }

        return $gradeBookJSONModel;
    }
}
