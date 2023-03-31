<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository\GradeBookDataRepository;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\ORMException;

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
     * @param GradeBook $gradeBook
     * @param int|null $expectedVersion
     *
     * @return GradeBookData
     * @throws ORMException
     */
    public function getGradeBookData(GradeBook $gradeBook, ?int $expectedVersion = null)
    {
        return $this->getGradeBookDataById($gradeBook->getActiveGradeBookDataId(), $expectedVersion);
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
    public function getGradeBookDataById(int $gradeBookDataId, ?int $expectedVersion = null)
    {
        return $this->gradeBookDataRepository->findGradeBookDataById($gradeBookDataId, $expectedVersion);
    }

    /**
     * @param GradeBookData $gradeBookData
     *
     * @throws ORMException
     */
    public function saveGradeBookData(GradeBookData $gradeBookData)
    {
        $gradeBookData->setLastUpdated(new \DateTime());

        $this->gradeBookDataRepository->saveGradeBookData($gradeBookData);
    }

    /**
     * @param int $gradeBookDataId
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteGradeBookData(int $gradeBookDataId)
    {
        $gradeBookData = $this->getGradeBookDataById($gradeBookDataId);
        $this->gradeBookDataRepository->deleteGradeBookData($gradeBookData);
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $userId
     *
     * @return GradeBookScore[]|ArrayCollection|Collection
     */
    public function getGradeBookScoresByUserId(GradeBookData $gradeBookData, int $userId)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('targetUserId', $userId));
        return $gradeBookData->getGradeBookScores()->matching($criteria);
    }

    /**
     * @param GradeBookData $gradebookData
     * @param array $publicationItems
     *
     * @return bool
     */
    public function completeGradeBookData(GradeBookData $gradebookData, array $publicationItems): bool
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
                if ($gradebookItem->getTitle() != $publicationItem->getTitle())
                {
                    $shouldUpdate = true;
                }
                $gradebookItem->setTitle($publicationItem->getTitle());
                $gradebookItem->setBreadcrumb($publicationItem->getBreadcrumb());
                unset($gradebookItems[$hash]);
            }
            else
            {
                // add new items to the gradebook.
                $gradebookItem = $publicationItem;
                $gradebookItem->setGradeBookData($gradebookData);
                $gradebookItem->setTitle($publicationItem->getTitle());
                $shouldUpdate = true;
            }
        }

        foreach ($gradebookItems as $key => $gradebookItem) // $gradebookItems still in the array refer to items that have been removed
        {
            if (empty($gradebookItem->getGradeBookColumn())) // should be safe to remove from database
            {
                $gradebookData->removeGradeBookItem($gradebookItem);
                $shouldUpdate = true;
            }
            elseif (!$gradebookItem->isRemoved())
            {
                $gradebookItem->setIsRemoved(true);
                $shouldUpdate = true;
            }
        }

        return $shouldUpdate;

        /*if ($canUpdate && $shouldUpdate)
        {
            $this->gradeBookService->saveGradeBookData($gradebookData);
        }*/
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     *
     * @return string
     */
    protected function getContextHash(ContextIdentifier $contextIdentifier): string
    {
        return md5($contextIdentifier->getContextClass() . ':' . $contextIdentifier->getContextId());
    }
}
