<?php
namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricNode;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests the RubricNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricNodeTest extends ChamiloTestCase
{
    /**
     * @var RubricData
     */
    protected $rubricData;

    /**
     * @var RubricNode
     */
    protected $testNode;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->rubricData = new RubricData('Test Rubric');
        $this->testNode = $this->rubricData->getRootNode();
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->rubricNode);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testAddRubricNode()
    {
        $this->testNode->addChild(new RubricNode('Test rubric 1', $this->rubricData));
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testAddClusterNode()
    {
        $this->testNode->addChild(new ClusterNode('Test cluster 1', $this->rubricData));
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testAddCategoryNode()
    {
        $this->testNode->addChild(new CategoryNode('Test category 1', $this->rubricData));
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testAddCriteriumNode()
    {
        $this->testNode->addChild(new CriteriumNode('Test criterium 1', $this->rubricData));
        $this->assertTrue(true);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testInsertRubricNode()
    {
        $this->testNode->insertChild(new RubricNode('Test rubric 1', $this->rubricData), 1);
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testInsertClusterNode()
    {
        $this->testNode->insertChild(new ClusterNode('Test cluster 1', $this->rubricData), 1);
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testInsertCategoryNode()
    {
        $this->testNode->insertChild(new CategoryNode('Test category 1', $this->rubricData), 1);
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testInsertCriteriumNode()
    {
        $this->testNode->insertChild(new CriteriumNode('Test criterium 1', $this->rubricData), 1);
        $this->assertTrue(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testSetParentNodeWithRootNode()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);

        $this->testNode->setParentNode($treeNode);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testSetChildrenWithRubricNode()
    {
        $this->testNode->setChildren(new ArrayCollection([new RubricNode('Test rubric 1', $this->rubricData)]));
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testSetChildrenWithClusterNode()
    {
        $this->testNode->setChildren(new ArrayCollection([new ClusterNode('Test cluster 1', $this->rubricData)]));
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testSetChildrenWithCategoryNode()
    {
        $this->testNode->setChildren(new ArrayCollection([new CategoryNode('Test category 1', $this->rubricData)]));
        $this->assertTrue(true);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testSetChildrenWithCriteriumNode()
    {
        $this->testNode->setChildren(new ArrayCollection([new CriteriumNode('Test criterium 1', $this->rubricData)]));
        $this->assertTrue(true);
    }
}

