<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookItemScoreServiceManager
{
    /**
     * @var GradeBookItemScoreServiceInterface[]
     */
    protected $scoreServicesByType;

    /**
     * GradeBookItemScoreServiceManager constructor.
     */
    public function __construct()
    {
        $this->scoreServicesByType = [];
    }

    /**
     * @param string $scoreServiceType
     * @param GradeBookItemScoreServiceInterface $scoreService
     */
    public function addScoreService(string $scoreServiceType, GradeBookItemScoreServiceInterface $scoreService)
    {
        $this->scoreServicesByType[$scoreServiceType] = $scoreService;
    }

    /**
     * @param int $scoreServiceType
     *
     * @return GradeBookItemScoreServiceInterface
     */
    public function getScoreServiceByType(string $scoreServiceType): GradeBookItemScoreServiceInterface
    {
        if (!array_key_exists($scoreServiceType, $this->scoreServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given score service %s is not supported', $scoreServiceType));
        }
        return $this->scoreServicesByType[$scoreServiceType];
    }
}