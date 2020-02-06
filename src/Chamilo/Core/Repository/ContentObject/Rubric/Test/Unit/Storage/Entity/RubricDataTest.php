<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricNode;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the RubricData
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricDataTest extends ChamiloTestCase
{
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
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->rubricData);
    }

    public function testCreateRubricCreatesTreeNode()
    {
        $this->assertInstanceOf(RubricNode::class, $this->rubricData->getRootNode());
    }

    public function testCreateRubricSetsTitle()
    {
        $this->assertEquals('Test Rubric', $this->rubricData->getRootNode()->getTitle());
    }

    public function testGetSetUseScores()
    {
        $this->rubricData->setUseScores(false);
        $this->assertFalse($this->rubricData->useScores());
    }

    public function testGetSetId()
    {
        $this->rubricData->setId(5);
        $this->assertEquals(5, $this->rubricData->getId());
    }

    public function testGetSetVersion()
    {
        $this->rubricData->setVersion(10);
        $this->assertEquals(10, $this->rubricData->getVersion());
    }

    public function testGetSetLastUpdated()
    {
        $lastUpdated = new \DateTime();
        $this->rubricData->setLastUpdated($lastUpdated);
        $this->assertEquals($lastUpdated, $this->rubricData->getLastUpdated());
    }

    public function testRemoveTreeNodeRemovesRubricDataFromNode()
    {
        $rootNode = $this->rubricData->getRootNode();
        $this->rubricData->removeTreeNode($rootNode);
        $this->assertNull($rootNode->getRubricData());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testRemoveTreeNodeNotInTreeNodes()
    {
        $newRubricData = new RubricData('test');
        $clusterNode = new ClusterNode('Test Clusternode', $newRubricData);

        $this->rubricData->removeTreeNode($clusterNode);
        $this->assertNotContains($clusterNode, $this->rubricData->getTreeNodes());
    }

    public function testRemoveChoiceRemovesRubricDataFromChoice()
    {
        $choice = new Choice($this->rubricData);
        $this->rubricData->removeChoice($choice);
        $this->assertNull($choice->getRubricData());
    }

    public function testRemoveLevelRemovesRubricDataFromLevel()
    {
        $level = new Level($this->rubricData);
        $this->rubricData->removeLevel($level);
        $this->assertNull($level->getRubricData());
    }

    public function testAddLevelTwice()
    {
        $level = new Level($this->rubricData);
        $this->rubricData->addLevel($level);
        $this->rubricData->addLevel($level);

        $this->assertCount(1, $this->rubricData->getLevels());
    }

    public function testRemoveLevelNotInLevels()
    {
        $newRubricData = new RubricData('test');
        $level = new Level($newRubricData);
        $this->rubricData->removeLevel($level);

        $this->assertCount(0, $this->rubricData->getLevels());
    }

    public function testAddChoiceTwice()
    {
        $choice = new Choice($this->rubricData);
        $this->rubricData->addChoice($choice);
        $this->rubricData->addChoice($choice);

        $this->assertCount(1, $this->rubricData->getChoices());
    }

    public function testRemoveChoiceNotInChoices()
    {
        $newRubricData = new RubricData('test');
        $choice = new Choice($newRubricData);
        $this->rubricData->removeChoice($choice);

        $this->assertCount(0, $this->rubricData->getChoices());
    }


}

