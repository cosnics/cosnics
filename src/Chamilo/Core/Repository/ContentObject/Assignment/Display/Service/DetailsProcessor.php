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
class DetailsProcessor
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
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    private $note;

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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     * @param string[] $submittedValues
     */
    public function __construct(
        AssignmentDataProvider $assignmentDataProvider, User $user, Entry $entry,
        Score $score = null, Note $note = null, $submittedValues
    )
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->user = $user;
        $this->entry = $entry;
        $this->score = $score;
        $this->note = $note;
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     */
    public function setScore(Score $score)
    {
        $this->score = $score;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     */
    public function setNote(Note $note)
    {
        $this->note = $note;
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
     * @param string []
     */
    public function setSubmittedValues($submittedValues)
    {
        $this->submittedValues = $submittedValues;
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

        if (!$this->processNote())
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

    /**
     *
     * @return boolean
     */
    protected function processNote()
    {
        $note = $this->getNote();

        $submittedValues = $this->getSubmittedValues();
        $submittedNote = $submittedValues[Note::PROPERTY_NOTE];

        if ($note instanceof Note)
        {
            if ($note->getNote() != $submittedNote)
            {
                $note->setNote($submittedNote);
                $note->setModified(time());
                $note->setUserId($this->getUser()->getId());

                return $this->getAssignmentDataProvider()->updateNote($note);
            }
        }
        else
        {
            return $this->getAssignmentDataProvider()->createNote($this->getEntry(), $this->getUser(), $submittedNote);
        }

        return true;
    }
}