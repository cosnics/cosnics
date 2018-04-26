<?php
/**
 * Created by PhpStorm.
 * User: pjbro
 * Date: 19/04/18
 * Time: 10:57
 */

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;


use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class ScoreService
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 */
class ScoreService
{
    /**
     * @var AssignmentDataProvider
     */
    protected $assignmentDataProvider;

    /**
     * ScoreService constructor.
     * @param AssignmentDataProvider $assignmentDataProvider
     */
    public function __construct(
        AssignmentDataProvider $assignmentDataProvider
    )
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
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

            return $this->assignmentDataProvider->createScore(
                $score
            );
        }

        $this->assignmentDataProvider->updateScore($score);

        return $score;
    }
}