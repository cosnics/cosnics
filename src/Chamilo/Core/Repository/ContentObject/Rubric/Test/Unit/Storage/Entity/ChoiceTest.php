<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Choice
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChoiceTest extends ChamiloTestCase
{
    /**
     * @var Choice
     */
    protected $choice;

    /**
     * @var RubricData
     */
    protected $rubricData;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->rubricData = new RubricData('Test Rubric');
        $this->choice = new Choice($this->rubricData);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->choice);
        unset($this->rubricData);
    }

    public function testChoiceIsAddedToRubricData()
    {
        $this->assertContains($this->choice, $this->rubricData->getChoices());
    }

    public function testSetGetId()
    {
        $this->choice->setId(5);
        $this->assertEquals(5, $this->choice->getId());
    }

    public function testSetGetSelected()
    {
        $this->choice->setSelected(true);
        $this->assertEquals(5, $this->choice->isSelected());
    }

    public function testGetSetHasFixedScore()
    {
        $this->choice->setHasFixedScore(true);
        $this->assertEquals(true, $this->choice->hasFixedScore());
    }

    public function testGetSetFixedScore()
    {
        $this->choice->setFixedScore(5);
        $this->assertEquals(5, $this->choice->getFixedScore());
    }

    public function testGetSetFeedback()
    {
        $this->choice->setFeedback('test feedback');
        $this->assertEquals('test feedback', $this->choice->getFeedback());
    }

    public function testSetGetLevel()
    {
        $level = new Level($this->rubricData);

        $this->choice->setLevel($level);
        $this->assertEquals($level, $this->choice->getLevel());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testSetCriteriumAddsChoiceToCriterium()
    {
        $criterium = new CriteriumNode('Test Criterium', $this->rubricData);
        $this->choice->setCriterium($criterium);
        $this->assertContains($this->choice, $criterium->getChoices());
    }

    //TODO when change rubric data remove from old rubric data

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testChangeCriteriumNodeRemovesChoiceFromOldCriterium()
    {
        $criterium = new CriteriumNode('Test Criterium', $this->rubricData);
        $criterium2 = new CriteriumNode('Test Criterium 2', $this->rubricData);

        $this->choice->setCriterium($criterium);
        $this->choice->setCriterium($criterium2);

        $this->assertNotContains($this->choice, $criterium->getChoices());
    }

    public function testChangeRubricDataRemovesChoiceFromOldRubricData()
    {
        $newRubricData = new RubricData('Rubric 2');
        $this->choice->setRubricData($newRubricData);

        $this->assertNotContains($this->choice, $this->rubricData->getChoices());
    }
}

