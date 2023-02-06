<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ScoreServiceManager
{
    /**
     * @var ScoreServiceInterface[]
     */
    protected $scoreServicesByType;

    /**
     * @var LearningPathScoreServiceInterface[]
     */
    protected $learningPathScoreServicesByType;

    /**
     * GradeBookItemScoreServiceManager constructor.
     */
    public function __construct()
    {
        $this->scoreServicesByType = [];
        $this->learningPathScoreServicesByType = [];
    }

    /**
     * @param string $scoreServiceType
     * @param ScoreServiceInterface $scoreService
     */
    public function addScoreService(string $scoreServiceType, ScoreServiceInterface $scoreService)
    {
        $this->scoreServicesByType[$scoreServiceType] = $scoreService;
    }

    /**
     * @param string $scoreServiceType
     * @param LearningPathScoreServiceInterface $scoreService
     */
    public function addLearningPathScoreService(string $scoreServiceType, LearningPathScoreServiceInterface $scoreService)
    {
        $this->learningPathScoreServicesByType[$scoreServiceType] = $scoreService;
    }

    /**
     * @param string $scoreServiceType
     *
     * @return ScoreServiceInterface
     */
    public function getScoreServiceByType(string $scoreServiceType): ScoreServiceInterface
    {
        if (!array_key_exists($scoreServiceType, $this->scoreServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given score service %s is not supported', $scoreServiceType));
        }
        return $this->scoreServicesByType[$scoreServiceType];
    }

    /**
     * @param string $scoreServiceType
     *
     * @return LearningPathScoreServiceInterface
     */
    public function getLearningPathScoreServiceByType(string $scoreServiceType): LearningPathScoreServiceInterface
    {
        if (!array_key_exists($scoreServiceType, $this->learningPathScoreServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given learning path score service %s is not supported', $scoreServiceType));
        }
        return $this->learningPathScoreServicesByType[$scoreServiceType];
    }
}