<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Domain\Storage\Entity;

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

    public function testRemoveChild()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $this->rootNode->removeChild($clusterNode2);

        $this->assertCount(1, $this->rootNode->getChildren());
    }
}


