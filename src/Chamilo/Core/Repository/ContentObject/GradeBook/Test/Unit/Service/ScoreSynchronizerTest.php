<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\AbsentScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\AuthAbsentScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\NullScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\ScoreSynchronizer;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ScoreSynchronizerTest extends ChamiloTestCase
{
    /** @var GradeBookData */
    protected $gradeBookData;

    /** @var GradeBookColumn */
    protected $column;

    /** @var GradeBookItem */
    protected $item;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $this->gradeBookData = new GradeBookData('gradebook');
        $this->column = new GradeBookColumn($this->gradeBookData);
        $this->column->setId(1);
        $this->item = new GradeBookItem($this->gradeBookData, $this->column);
        $this->item->setId(1);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->item);
        unset($this->column);
        unset($this->gradeBookData);
    }

    public function testScoreSynchronizerWithEmptyGradeBook()
    {
        $gradeBookData = new GradeBookData('gradebook');
        $scoreSynchronizer = new ScoreSynchronizer($gradeBookData, [], []);
        $this->assertEmpty($scoreSynchronizer->getAddScores());
        $this->assertEmpty($scoreSynchronizer->getRemoveScores());
        $this->assertEmpty($scoreSynchronizer->getUpdateScores());
    }

    public function testScoreSynchronizerScoreWithoutSetColumn()
    {
        $score = new GradeBookScore();
        $score->setGradeBookData($this->gradeBookData);
        $score->setTargetUserId(22);
        $score->setGradeBookColumn(null);
        $scoreSynchronizer = new ScoreSynchronizer($this->gradeBookData, [], []);
        $this->assertEmpty($scoreSynchronizer->getUpdateScores());
        $this->assertEmpty($scoreSynchronizer->getAddScores());
        $this->assertEquals([$score], $scoreSynchronizer->getRemoveScores());
    }

    public function testScoreSynchronizerItemPresedence()
    {
        $this->column->setType('group');
        $item2 = new GradeBookItem($this->gradeBookData, $this->column);
        $item2->setId(2);

        $gradeScore75 = new GradeScore(75);
        $gradeScore85 = new GradeScore(85);
        $gradeScore90 = new GradeScore(90);

        $scoreSynchronizer = new ScoreSynchronizer($this->gradeBookData, [1 => [22 => $gradeScore85], 2 => [22 => $gradeScore90]], [22]);
        $this->assertEmpty($scoreSynchronizer->getRemoveScores());
        $this->assertEmpty($scoreSynchronizer->getUpdateScores());
        $this->assertEquals([[$this->column, 22, $item2, $gradeScore90]], $scoreSynchronizer->getAddScores());

        $scoreSynchronizer = new ScoreSynchronizer($this->gradeBookData, [1 => [22 => $gradeScore85], 2 => [22 => $gradeScore75]], [22]);
        $this->assertEquals([[$this->column, 22, $this->item, $gradeScore85]], $scoreSynchronizer->getAddScores());
    }

    public function testScoreSynchronizerScoreToBeRemoved()
    {
        $score = new GradeBookScore();
        $score->setGradeBookData($this->gradeBookData);
        $score->setGradeBookColumn($this->column);
        $score->setTargetUserId(22);
        $scoreSynchronizer = new ScoreSynchronizer($this->gradeBookData, [], []);
        $this->assertEmpty($scoreSynchronizer->getUpdateScores());
        $this->assertEmpty($scoreSynchronizer->getAddScores());
        $this->assertEquals([$score], $scoreSynchronizer->getRemoveScores());
    }

    public function testScoreSynchronizerScoreToBeAdded()
    {
        $scoreSynchronizer = new ScoreSynchronizer($this->gradeBookData, [1 => [22 => new NullScore()]], [22]);
        $this->assertEmpty($scoreSynchronizer->getRemoveScores());
        $this->assertEmpty($scoreSynchronizer->getUpdateScores());
        $this->assertEquals([[$this->column, 22, $this->item, new NullScore()]], $scoreSynchronizer->getAddScores());
    }

    public function testScoreSynchronizerScoreToBeUpdated()
    {
        $gradeScore = new GradeScore(85);
        $nullScore = new NullScore();
        $absentScore = new AbsentScore();
        $authAbsentScore = new AuthAbsentScore();

        $score = new GradeBookScore();
        $score->setGradeBookData($this->gradeBookData);
        $score->setGradeBookColumn($this->column);
        $score->setTargetUserId(22);
        $score->setSourceScore(null);
        $score->setSourceScoreAbsent(true);

        $this->_testScoreToBeUpdatedWithGradeScore($score, $gradeScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $nullScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $authAbsentScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $absentScore, true);

        $score->setSourceScoreAbsent(false);
        $score->setSourceScoreAuthAbsent(true);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $gradeScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $nullScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $absentScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $authAbsentScore, true);

        $score->setSourceScoreAuthAbsent(false);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $gradeScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $absentScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $authAbsentScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $nullScore, true);

        $score->setSourceScore(85);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $absentScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $authAbsentScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $nullScore);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $gradeScore, true);

        $score->setSourceScore(80);
        $this->_testScoreToBeUpdatedWithGradeScore($score, $gradeScore);
    }

    /**
     * @param GradeBookScore $score
     * @param GradeScoreInterface $gradeScore
     * @param bool $expectEmpty
     */
    protected function _testScoreToBeUpdatedWithGradeScore(GradeBookScore $score, GradeScoreInterface $gradeScore, bool $expectEmpty = false)
    {
        $scoreSynchronizer = new ScoreSynchronizer($this->gradeBookData, [1 => [22 => $gradeScore]], [22]);
        $this->assertEmpty($scoreSynchronizer->getAddScores());
        $this->assertEmpty($scoreSynchronizer->getRemoveScores());
        $this->assertEquals($expectEmpty ? [] : [[$score, $this->item, $gradeScore]], $scoreSynchronizer->getUpdateScores());
    }
}