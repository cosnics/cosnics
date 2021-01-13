<?php
/**
 * Created by PhpStorm.
 * User: pjbro
 * Date: 19/04/18
 * Time: 10:57
 */

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;


use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class ScoreService
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 */
class ScoreService
{
    /**
     * @var AssignmentServiceBridgeInterface
     */
    protected $assignmentServiceBridge;

    /**
     * ScoreService constructor.
     *
     * @param AssignmentServiceBridgeInterface $assignmentServiceBridge
     */
    public function __construct(
        AssignmentServiceBridgeInterface $assignmentServiceBridge
    )
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
    }

    /**
     * @param Score $score
     * @param User $user
     * @return Score
     */
    public function createOrUpdateScoreByUser(Score $score, User $user)
    {
        if($score->getUserId() !== $user->getId()) {
            $score->setUserId($user->getId());
        }

        if(empty($score->getId())){
            $score->setModified(time());

            return $this->assignmentServiceBridge->createScore(
                $score
            );
        }

        $this->assignmentServiceBridge->updateScore($score);

        return $score;
    }

    /**
     * @param Entry $entry
     * @param int $totalScore
     * @param User $user
     *
     * @return Score
     */
    public function createOrUpdateScoreForEntry(Entry $entry, int $totalScore, User $user)
    {
        $score = $this->assignmentServiceBridge->findScoreByEntry($entry);
        if(!$score instanceof Score)
        {
            $score = $this->assignmentServiceBridge->initializeScore();
            $score->setEntryId($entry->getId());
        }

        $score->setScore($totalScore);

        return $this->createOrUpdateScoreByUser($score, $user);
    }
}
