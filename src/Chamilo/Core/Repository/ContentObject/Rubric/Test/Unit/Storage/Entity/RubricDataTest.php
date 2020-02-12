<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
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

    public function testGetSetContentObjectId()
    {
        $this->rubricData->setContentObjectId(8);
        $this->assertEquals(8, $this->rubricData->getContentObjectId());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
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

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetTreeNodeById()
    {
        $treeNode = new CategoryNode('Test', $this->rubricData);
        $treeNode->setId(15);

        $this->assertEquals($treeNode, $this->rubricData->getTreeNodeById(15));
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetParentNodeById()
    {
        $treeNode = new CategoryNode('Test', $this->rubricData);
        $treeNode->setId(15);

        $this->assertEquals($treeNode, $this->rubricData->getParentNodeById(15));
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetParentNodeByIdWhenNoIdSet()
    {
        $this->assertEquals($this->rubricData->getRootNode(), $this->rubricData->getParentNodeById(null));
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetTreeNodeByIdFails()
    {
        $this->rubricData->getTreeNodeById(15);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetLevelById()
    {
        $level = new Level($this->rubricData);
        $level->setId(15);

        $this->assertEquals($level, $this->rubricData->getLevelById(15));
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetLevelByIdFails()
    {
        $this->rubricData->getLevelById(15);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetChoiceById()
    {
        $choice = new Choice($this->rubricData);
        $choice->setId(15);

        $this->assertEquals($choice, $this->rubricData->getChoiceById(15));
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testGetChoiceByIdFails()
    {
        $this->rubricData->getChoiceById(15);
    }

    public function testAddLevelSetsCorrectSort()
    {
        new Level($this->rubricData);
        $level2 = new Level($this->rubricData);

        $this->assertEquals(2, $level2->getSort());
    }

    public function testRemoveLevelCleansUpSort()
    {
        new Level($this->rubricData); // Level 1
        $level2 = new Level($this->rubricData); // Level 2
        $level3 = new Level($this->rubricData);

        $this->rubricData->removeLevel($level2);

        $this->assertEquals(2, $level3->getSort());
    }

    public function testMoveLevelSetsCorrectSort()
    {
        new Level($this->rubricData); // Level 1
        $level2 = new Level($this->rubricData); // Level 2
        $level3 = new Level($this->rubricData);
        $level4 = new Level($this->rubricData);

        $this->rubricData->moveLevel($level2, 3);

        $this->assertEquals(3, $level2->getSort());
        $this->assertEquals(2, $level3->getSort());
        $this->assertEquals(4, $level4->getSort());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMoveLevelNotInLevels()
    {
        $rubricData2 = new RubricData('test2');
        $level = new Level($rubricData2);

        $this->rubricData->moveLevel($level, 2);
    }

    public function testInsertLevelTwice()
    {
        $level = new Level($this->rubricData);
        $this->rubricData->insertLevel($level, 2);

        $this->assertCount(1, $this->rubricData->getLevels());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testAddLevelCreatesNewChoiceInCriteriumNode()
    {
        $criteriumNode = new CriteriumNode('test criterium', $this->rubricData);
        $level = new Level($this->rubricData);

        $this->assertEquals($level, $criteriumNode->getChoices()->first()->getLevel());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testInsertLevelCreatesNewChoiceInCriteriumNode()
    {
        $criteriumNode = new CriteriumNode('test criterium', $this->rubricData);
        $level = new Level(new RubricData('temp'));

        $this->rubricData->insertLevel($level, 1);

        $this->assertEquals($level, $criteriumNode->getChoices()->first()->getLevel());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testRemoveLevelRemovesChoiceInCriteriumNode()
    {
        $criteriumNode = new CriteriumNode('test criterium', $this->rubricData);
        $level = new Level($this->rubricData);

        $this->assertCount(1, $criteriumNode->getChoices());

        $this->rubricData->removeLevel($level);

        $this->assertCount(0, $criteriumNode->getChoices());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testAddTreeNodeInsertsChoicesForLevel()
    {
        $level = new Level($this->rubricData);
        $criteriumNode = new CriteriumNode('test criterium', $this->rubricData);

        $this->assertEquals($level, $criteriumNode->getChoices()->first()->getLevel());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testRemoveTreeNodeRemovesChoices()
    {
        new Level($this->rubricData);
        $criteriumNode = new CriteriumNode('test criterium', $this->rubricData);

        $this->rubricData->removeTreeNode($criteriumNode);

        $this->assertCount(0, $criteriumNode->getChoices());
        $this->assertCount(0, $this->rubricData->getChoices());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testGetClusterNodes()
    {
        new ClusterNode('Test Cluster', $this->rubricData);
        new CategoryNode('Test Category 1', $this->rubricData);
        new CategoryNode('Test Category 2', $this->rubricData);
        new CriteriumNode('Test Criterium 1', $this->rubricData);
        new CriteriumNode('Test Criterium 2', $this->rubricData);
        new CriteriumNode('Test Criterium 3', $this->rubricData);

        $this->assertCount(1, $this->rubricData->getClusterNodes());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testGetCategoryNodes()
    {
        new ClusterNode('Test Cluster', $this->rubricData);
        new CategoryNode('Test Category 1', $this->rubricData);
        new CategoryNode('Test Category 2', $this->rubricData);
        new CriteriumNode('Test Criterium 1', $this->rubricData);
        new CriteriumNode('Test Criterium 2', $this->rubricData);
        new CriteriumNode('Test Criterium 3', $this->rubricData);

        $this->assertCount(2, $this->rubricData->getCategoryNodes());
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testGetCriteriumNodes()
    {
        new ClusterNode('Test Cluster', $this->rubricData);
        new CategoryNode('Test Category 1', $this->rubricData);
        new CategoryNode('Test Category 2', $this->rubricData);
        new CriteriumNode('Test Criterium 1', $this->rubricData);
        new CriteriumNode('Test Criterium 2', $this->rubricData);
        new CriteriumNode('Test Criterium 3', $this->rubricData);

        $this->assertCount(3, $this->rubricData->getCriteriumNodes());
    }



}

