<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ScoreFormProcessor
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    private $entry;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    private $score;

    /**
     *
     * @var string[]
     */
    private $submittedValues;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     * @param string[] $submittedValues
     */
    public function __construct(
        AssignmentDataProvider $assignmentDataProvider, User $user, Entry $entry,
        Score $score = null, $submittedValues
    )
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->user = $user;
        $this->entry = $entry;
        $this->score = $score;
        $this->submittedValues = $submittedValues;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    public function getAssignmentDataProvider()
    {
        return $this->assignmentDataProvider;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     *
     * @return string[]
     */
    public function getSubmittedValues()
    {
        return $this->submittedValues;
    }

    /**
     *
     * @return boolean
     */
    public function run()
    {
        if (!$this->processScore())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    protected function processScore()
    {
        $score = $this->getScore();

        $submittedValues = $this->getSubmittedValues();
        $submittedScore = $submittedValues[Score::PROPERTY_SCORE];

        if ($score instanceof Score)
        {
            if ($score->getScore() != $submittedScore)
            {
                $score->setScore($submittedScore);
                $score->setModified(time());
                $score->setUserId($this->getUser()->getId());

                return $this->getAssignmentDataProvider()->updateScore($score);
            }
        }
        else
        {
            return $this->getAssignmentDataProvider()->createScore(
                $this->getEntry(), $this->getUser(), $submittedScore
            );
        }

        return true;
    }
}