<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
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
    public function setUp()
    {
        $this->rubricData = new RubricData('Test Rubric');
        $this->level = new Level($this->rubricData);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
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

    public function testChangeRubricDataRemovesChoiceFromOldRubricData()
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
}

