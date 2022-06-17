<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

/*use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\RubricHasResultsException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\Level;*/
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
}
