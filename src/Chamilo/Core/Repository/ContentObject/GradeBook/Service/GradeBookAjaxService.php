<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

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
     */
    public function __construct(GradeBookService $gradeBookService)
    {
        $this->gradeBookService = $gradeBookService;
        $this->serializer = $this->createSerializer();
    }

    /**
     * @param GradeBook $gradebook
     *
     * @return GradeBookData
     * @throws \Doctrine\ORM\ORMException
     */
    public function getGradeBookData(GradeBook $gradebook)
    {
        return $this->gradeBookService->getGradeBook($gradebook->getActiveGradeBookDataId(), null);
    }

    /**
     * @param GradeBookData $gradebookData
     * @param GradeBookItem[] $publicationItems
     *
     */
    public function updateGradeBookData(GradeBookData $gradebookData, array $publicationItems)
    {
        $shouldUpdate = false;
        $gradebookItems = array();
        foreach ($gradebookData->getGradeBookItems() as $gradebookItem)
        {
            $hash = $this->getContextHash($gradebookItem->getContextIdentifier());
            $gradebookItems[$hash] = $gradebookItem;
        }

        foreach ($publicationItems as $publicationItem)
        {
            $hash = $this->getContextHash($publicationItem->getContextIdentifier());
            $gradebookItem = $gradebookItems[$hash];
            if (!empty($gradebookItem))
            {
                $gradebookItem->setType($publicationItem->getType());
                $gradebookItem->setTitle($publicationItem->getTitle());
                $gradebookItem->setBreadcrumb($publicationItem->getBreadcrumb());
                unset($gradebookItems[$hash]);
            }
            else
            {
                // add new items to the gradebook.
                $gradebookItem = $publicationItem;
                $gradebookItem->setGradeBookData($gradebookData);
                $shouldUpdate = true;
            }
        }
        foreach ($gradebookItems as $key => $gradebookItem)
        {
            // $gradebookItems still in the array refer to items that have been removed
            // todo: update the corresponding data structure of the gradebook.
        }

        if ($shouldUpdate)
        {
            $this->gradeBookService->saveGradeBook($gradebookData);
        }
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     *
     * @return string
     */
    public function getContextHash(ContextIdentifier $contextIdentifier): string
    {
        return md5($contextIdentifier->getContextClass() . ':' . $contextIdentifier->getContextId());
    }

    /**
     * @param GradeBookData $gradebookData
     *
     * @return array
     */
    public function getGradeBookObjectData(GradeBookData $gradebookData): array
    {
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
        $jsonModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $category = $gradebookData->getGradeBookCategoryById($jsonModel->getId());
        $jsonModel->updateGradeBookCategory($category);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'category' => $jsonModel::fromGradeBookCategory($category)
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
            'category' => GradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param string $gradeBookCategoryJSONData
     *
     * @return GradeBookCategoryJSONModel
     */
    protected function parseGradeBookCategoryJSONModel(string $gradeBookCategoryJSONData)
    {
        $jsonModel = $this->serializer->deserialize(
            $gradeBookCategoryJSONData, GradeBookCategoryJSONModel::class, 'json'
        );

        if (!$jsonModel instanceof GradeBookCategoryJSONModel)
        {
            throw new \RuntimeException('Could not parse the gradebook category JSON model');
        }

        return $jsonModel;
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookColumnJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addGradeBookColumn(int $gradeBookDataId, int $versionId, string $gradeBookColumnJSONData)
    {
        $jsonModel = $this->parseGradeBookColumnJSONModel($gradeBookColumnJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $column = $jsonModel->toGradeBookColumn($gradebookData);
        $column->setGradeBookCategory(null);
        $gradebookItemId = $jsonModel->getSubItemIds()[0];
        $gradeItem = $gradebookData->getGradeBookItemById($gradebookItemId);
        $gradeItem->setGradeBookColumn($column);
        $gradebookData->addGradeBookColumn($column);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($column)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int $gradeItemId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function addGradeBookColumnSubItem(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, int $gradeItemId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeItem = $gradebookData->getGradeBookItemById($gradeItemId);
        $oldGradeBookColumn = $gradeItem->getGradeBookColumn();
        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);
        if ($oldGradeBookColumn instanceof GradeBookColumn)
        {
            if ($oldGradeBookColumn->getType() == 'group')
            {
                throw new \RuntimeException('Grade item ' . $gradeItem->getId() . ' already belongs to a group');
            }
            $gradebookData->removeGradeBookColumn($oldGradeBookColumn);
        }
        $gradeBookColumn->setType('group');
        $gradeItem->setGradeBookColumn($gradeBookColumn);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($gradeBookColumn)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int $gradeItemId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeGradeBookColumnSubItem(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, int $gradeItemId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeItem = $gradebookData->getGradeBookItemById($gradeItemId);
        $gradeItemColumn = $gradeItem->getGradeBookColumn();
        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);

        if ($gradeItemColumn !== $gradeBookColumn)
        {
            throw new \RuntimeException('Grade item ' . $gradeItem->getId() . ' is not a subitem of column ' . $gradeBookColumn->getId());
        }

        $gradeItem->setGradeBookColumn(null);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($gradeBookColumn)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookColumnJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateGradeBookColumn(int $gradeBookDataId, int $versionId, string $gradeBookColumnJSONData)
    {
        $jsonModel = $this->parseGradeBookColumnJSONModel($gradeBookColumnJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $column = $gradebookData->getGradeBookColumnById($jsonModel->getId());
        $jsonModel->updateGradeBookColumn($column);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'gradebookColumn' => GradeBookColumnJSONModel::fromGradeBookColumn($column)
        ];
    }

    /**
     * @param string $gradeBookColumnJSONData
     *
     * @return GradeBookColumnJSONModel
     */
    protected function parseGradeBookColumnJSONModel(string $gradeBookColumnJSONData)
    {
        $jsonModel = $this->serializer->deserialize(
            $gradeBookColumnJSONData, GradeBookColumnJSONModel::class, 'json'
        );

        if (!$jsonModel instanceof GradeBookColumnJSONModel)
        {
            throw new \RuntimeException('Could not parse the gradebook column JSON model');
        }

        return $jsonModel;
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int|null $categoryId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function updateGradeBookColumnCategory(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, ?int $categoryId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $column = $gradebookData->updateGradeBookColumnCategory($gradeBookColumnId, $categoryId);
        $newCategory = $column->getGradeBookCategory();
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'gradebookColumnId' => $column->getId(),
            'categoryId' => empty($newCategory) ? null : $newCategory->getId(),
            'sort' => $column->getSort()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveGradeBookColumn(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, int $newSort)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);
        $gradebookData->moveGradeBookColumn($gradeBookColumn, $newSort);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'gradebookColumnId' => $gradeBookColumn->getId(),
            'sort' => $gradeBookColumn->getSort()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeGradeBookColumn(int $gradeBookDataId, int $versionId, int $gradeBookColumnId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);
        $gradebookData->removeGradeBookColumn($gradeBookColumn);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()]
        ];
    }

    /**
     * @return Serializer
     */
    private function createSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->setDeserializationContextFactory(function () {
                return DeserializationContext::create();
            })
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();
    }
}
