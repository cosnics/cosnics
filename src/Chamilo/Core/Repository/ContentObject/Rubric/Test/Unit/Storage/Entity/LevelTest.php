<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Level
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LevelTest extends ChamiloTestCase
{
    /**
     * @var Level
     */
    protected $level;

    /**
     * @var RubricData
     */
    protected $rubricData;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->rubricData = new RubricData('Test Rubric');
        $this->level = new Level($this->rubricData);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->rubricData);
        unset($this->level);
    }

    public function testLevelIsAddedToRubricData()
    {
        $this->assertContains($this->level, $this->rubricData->getLevels());
    }

    public function testSetGetId()
    {
        $this->level->setId(5);
        $this->assertEquals(5, $this->level->getId());
    }

    public function testSetGetTitle()
    {
        $title = 'test';
        $this->level->setTitle($title);

        $this->assertEquals($title, $this->level->getTitle());
    }

    public function testSetGetDescription()
    {
        $description = 'test';
        $this->level->setDescription($description);

        $this->assertEquals($description, $this->level->getDescription());
    }

    public function testSetGetScore()
    {
        $this->level->setScore(10);
        $this->assertEquals(10, $this->level->getScore());
    }

    public function testSetGetIsDefault()
    {
        $this->level->setIsDefault(true);
        $this->assertEquals(true, $this->level->isDefault());
    }

    public function testChangeRubricDataRemovesLevelFromOldRubricData()
    {
        $newRubricData = new RubricData('Rubric 2');
        $this->level->setRubricData($newRubricData);

        $this->assertNotContains($this->level, $this->rubricData->getLevels());
    }

    public function testSetGetSort()
    {
        $this->level->setSort(1);
        $this->assertEquals(1, $this->level->getSort());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSortInvalid()
    {
        $this->level->setSort(2);
    }

    public function testSortSetOnCreation()
    {
        $this->assertEquals(1, $this->level->getSort());
    }

    public function testLevelWithoutCriterium()
    {
        $this->assertFalse($this->level->hasCriterium());
    }

    public function testLevelWithCriterium()
    {
        $criterium = new CriteriumNode('Test criterium 1', $this->rubricData);
        $criterium->setId(8);
        $this->rubricData->getRootNode()->addChild($criterium);

        $level = new Level($this->rubricData, $criterium);
        $this->assertTrue($level->hasCriterium());
        $this->assertEquals(8, $level->getCriteriumId());
        $this->assertEquals(1, $criterium->getLevels()->count());
        $this->assertEquals(2, $this->rubricData->getLevels()->count());

        $criterium->addLevel($level);
        $this->assertEquals(1, $criterium->getLevels()->count());

        $level->setCriterium(null);
        $this->assertEquals(0, $criterium->getLevels()->count());
    }

    public function testCriteriumRemoveLevel()
    {
        $criterium = new CriteriumNode('Test criterium 1', $this->rubricData);
        $this->rubricData->getRootNode()->addChild($criterium);

        $level = new Level($this->rubricData, $criterium);
        $criterium->removeLevel($level);
        $this->assertNull($level->getCriterium());
    }

    public function testLevelWithCriteriumFromDifferentRubric()
    {
        $this->expectException(\InvalidArgumentException::class);

        $newRubricData = new RubricData('Rubric 2');
        $criterium = new CriteriumNode('Test criterium 1', $newRubricData);
        $newRubricData->getRootNode()->addChild($criterium);

        new Level($this->rubricData, $criterium);
    }

    public function testLevelWithCriteriumSetDifferentRubric()
    {
        $this->expectException(\InvalidArgumentException::class);

        $criterium = new CriteriumNode('Test criterium 1', $this->rubricData);
        $this->rubricData->getRootNode()->addChild($criterium);
        $level = new Level($this->rubricData, $criterium);
        $newRubricData = new RubricData('Rubric 2');
        $level->setRubricData($newRubricData);
    }
}

