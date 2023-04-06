<?php
namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
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
    protected function setUp(): void    {
        $this->rubricData = new RubricData('Test Rubric');
        $this->rubricData->getRootNode()->setId(8);
        $this->testNode = $this->rubricData->getRootNode();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
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

    /**
     * @throws \Exception
     */
    public function testFromJsonModel()
    {
        $jsonModel = new TreeNodeJSONModel(5, 'Test', TreeNodeJSONModel::TYPE_RUBRIC, 1);

        $clusterNode = RubricNode::fromJSONModel($jsonModel, $this->rubricData);
        $this->assertEquals('Test', $clusterNode->getTitle());
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @throws \Exception
     */
    public function testFromJsonModelWithBadType()
    {
        $jsonModel = new TreeNodeJSONModel(5, 'Test', TreeNodeJSONModel::TYPE_CATEGORY, 1);
        RubricNode::fromJSONModel($jsonModel, $this->rubricData);
    }

    /**
     * @throws \Exception
     */
    public function testToJSONModel()
    {
        $jsonModel = $this->testNode->toJSONModel();
        $this->assertInstanceof(TreeNodeJSONModel::class, $jsonModel);
        $this->assertEquals($jsonModel->getId(), 8);
        $this->assertEquals($jsonModel->getTitle(), 'Test Rubric');
    }

    /**
     * @throws \Exception
     */
    public function testUpdateFromJSONModel()
    {
        $jsonModel = new TreeNodeJSONModel(5, 'Test', TreeNodeJSONModel::TYPE_RUBRIC, 1);

        $categoryNode = $this->testNode->updateFromJSONModel($jsonModel);
        $this->assertEquals('Test', $categoryNode->getTitle());
    }

    /**
     * @throws \Exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateFromJSONModelWithBadType()
    {
        $jsonModel = new TreeNodeJSONModel(5, 'Test', TreeNodeJSONModel::TYPE_CATEGORY, 1);
        $this->testNode->updateFromJSONModel($jsonModel);
    }
}

