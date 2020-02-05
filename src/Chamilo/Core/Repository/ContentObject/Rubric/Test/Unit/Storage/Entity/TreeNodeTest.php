<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the TreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeTest extends ChamiloTestCase
{
    /**
     * @var TreeNode
     */
    protected $rootNode;

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
        $this->rootNode = $this->rubricData->getRootNode();

        $this->rootNode->addChild(new ClusterNode('Test Cluster 1', $this->rubricData));

        $clusterNode2 = new ClusterNode('Test Cluster 2', $this->rubricData);
        $this->rootNode->addChild($clusterNode2);

        $categoryNode = new CategoryNode('Test Category 1', $this->rubricData);
        $clusterNode2->addChild($categoryNode);

        $criteriumNode = new CriteriumNode('Test Criterium 1', $this->rubricData);
        $categoryNode->addChild($criteriumNode);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->rootNode);
        unset($this->rubricData);
    }

    /**
     * Tests the data setup. If this test fails than the basic method of adding a child fails and the tree test
     * data could not be created. In that case: look into the addChild method first.
     */
    public function testDataSetup()
    {
        $this->assertCount(2, $this->rootNode->getChildren());
        $this->assertCount(1, $this->rootNode->getChildren()[1]->getChildren());
        $this->assertCount(1, $this->rootNode->getChildren()[1]->getChildren()[0]->getChildren());
    }

    public function testAddChild()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->addChild($treeNode);

        $this->assertCount(3, $this->rootNode->getChildren());
        $this->assertEquals($treeNode, $this->rootNode->getChildren()[2]);
    }

    public function testAddChildTwice()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->addChild($treeNode);
        $this->rootNode->addChild($treeNode);

        $this->assertCount(3, $this->rootNode->getChildren());
    }

    public function testAddChildSetsCorrectSort()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->addChild($treeNode);

        $this->assertEquals(3, $treeNode->getSort());
    }

    public function testAddChildSetsCorrectParentNode()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->addChild($treeNode);

        $this->assertEquals($this->rootNode, $treeNode->getParentNode());
    }

    public function testAddChildSetsCorrectDepth()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->addChild($treeNode);

        $this->assertEquals(1, $treeNode->getDepth());
    }

    public function testRemoveChild()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $this->rootNode->removeChild($clusterNode2);

        $this->assertCount(1, $this->rootNode->getChildren());
        $this->assertNotContains($clusterNode2, $this->rootNode->getChildren());
    }

    public function testRemoveChildTwice()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $this->rootNode->removeChild($clusterNode2);
        $this->rootNode->removeChild($clusterNode2);

        $this->assertCount(1, $this->rootNode->getChildren());
    }

    public function testRemoveChildSetsCorrectSort()
    {
        $clusterNode1 = $this->rootNode->getChildren()->get(0);
        $this->rootNode->removeChild($clusterNode1);

        $this->assertEquals(1, $this->rootNode->getChildren()->get(1)->getSort());
    }

    public function testRemoveSetsCorrectParentNode()
    {
        $clusterNode1 = $this->rootNode->getChildren()->get(0);
        $this->rootNode->removeChild($clusterNode1);

        $this->assertNull($clusterNode1->getParentNode());
    }

    public function testInsertChild()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 2);

        $this->assertCount(3, $this->rootNode->getChildren());
        $this->assertEquals($treeNode, $this->rootNode->getChildren()[2]);
    }

    public function testInsertChildTwice()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 2);
        $this->rootNode->insertChild($treeNode, 2);

        $this->assertCount(3, $this->rootNode->getChildren());
    }

    public function testInsertChildSetsCorrectSort()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 2);

        $this->assertEquals(2, $treeNode->getSort());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInsertChildWithInvalidLowerSort()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInsertChildWithInvalidUpperSort()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 4);
    }

    public function testInsertChildSetsCorrectDepth()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 2);
        $this->assertEquals(1, $treeNode->getDepth());
    }

    public function testSetParentNode()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);

        $this->assertEquals($this->rootNode, $treeNode->getParentNode());
    }

    public function testSetParentNodeAddsToChildren()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);

        $this->assertEquals($treeNode, $this->rootNode->getChildren()->get(2));
    }

    public function testSetParentNodeTwice()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);
        $treeNode->setParentNode($this->rootNode);

        $this->assertCount(3, $this->rootNode->getChildren());
    }

    public function testSetParentNodeSetsCorrectSort()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);

        $this->assertEquals(3, $treeNode->getSort());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParentNodeWithRootNode()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);

        $this->rootNode->setParentNode($treeNode);
    }

    public function testSetParentNodeSetsCorrectDepth()
    {
        $treeNode = new ClusterNode('Test Cluster 3', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);

        $this->assertEquals(1, $treeNode->getDepth());
    }

    public function testChangeParentNode()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];

        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);
        $treeNode->setParentNode($clusterNode1);

        $this->assertEquals($clusterNode1, $treeNode->getParentNode());
    }

    public function testChangeParentNodeAddedToCorrectParent()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];

        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);
        $treeNode->setParentNode($clusterNode1);

        $this->assertContains($treeNode, $clusterNode1->getChildren());
    }

    public function testChangeParentNodeNotInOldParent()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];

        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);
        $treeNode->setParentNode($clusterNode1);

        $this->assertNotContains($treeNode, $this->rootNode->getChildren());
    }

    public function testChangeParentNodeSetsCorrectSort()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];

        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);

        $this->assertEquals(3, $treeNode->getSort());

        $treeNode->setParentNode($clusterNode1);

        $this->assertEquals(1, $treeNode->getSort());
    }

    public function testChangeParentNodeCleansUpSortsInOldParent()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];
        $clusterNode2 = $this->rootNode->getChildren()[1];

        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $this->rootNode->insertChild($treeNode, 2);

        $this->assertEquals(3, $clusterNode2->getSort());

        $treeNode->setParentNode($clusterNode1);

        $this->assertEquals(2, $clusterNode2->getSort());
    }

    public function testChangeParentNodeSetsCorrectDepth()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];

        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $treeNode->setParentNode($this->rootNode);
        $treeNode->setParentNode($clusterNode1);

        $this->assertEquals(2, $treeNode->getDepth());
    }

    public function testMoveChild()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];

        $this->rootNode->moveChild($clusterNode1, 2);

        $this->assertEquals(2, $clusterNode1->getSort());
    }

    public function testMoveChildFixesOrders()
    {
        $clusterNode1 = $this->rootNode->getChildren()[0];
        $clusterNode2 = $this->rootNode->getChildren()[1];

        $this->rootNode->moveChild($clusterNode1, 2);

        $this->assertEquals(1, $clusterNode2->getSort());
    }

    public function testTreeNodeIsAddedToRubricDataDuringConstruct()
    {
        $treeNode = new CriteriumNode('Test Criterium 2', $this->rubricData);
        $this->assertContains($treeNode, $this->rubricData->getTreeNodes());
    }
}


