<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\GradeBookItemScoreServiceInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssignmentScoreServiceManager
{
    /**
     * @var GradeBookItemScoreServiceInterface[]
     */
    protected $scoreServicesByType;

    /**
     * AssignmentScoreServiceManager constructor.
     */
    public function __construct()
    {
        $this->scoreServicesByType = [];
    }

    /**
     * @param int $entityType
     * @param GradeBookItemScoreServiceInterface $scoreService
     */
    public function addScoreService(int $entityType, GradeBookItemScoreServiceInterface $scoreService)
    {
        $this->scoreServicesByType[$entityType] = $scoreService;
    }

    /**
     * @param int $entityType
     *
     * @return GradeBookItemScoreServiceInterface
     */
    public function getScoreServiceByType(int $entityType): GradeBookItemScoreServiceInterface
    {
        if (!array_key_exists($entityType, $this->scoreServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given assignment score service %s is not supported', $entityType));
        }
        return $this->scoreServicesByType[$entityType];
    }
}