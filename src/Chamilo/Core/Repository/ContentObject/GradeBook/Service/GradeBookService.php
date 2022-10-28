<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookItemJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository\GradeBookDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

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
        $gradeBookData = $this->getGradeBook($gradeBookDataId);
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
}
