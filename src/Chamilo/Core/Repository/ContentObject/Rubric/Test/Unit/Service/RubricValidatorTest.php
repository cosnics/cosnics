<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricValidator;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the RubricValidator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricValidatorTest extends ChamiloTestCase
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
     * @var RubricValidator
     */
    protected $rubricValidator;

    /**
     * Setup before each test
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function setUp()
    {
        $this->rubricValidator = new RubricValidator();

        $this->rubricData = new RubricData('Test Rubric');
        $this->rootNode = $this->rubricData->getRootNode();
        $this->rootNode->setId(1);

        $this->rootNode->addChild(new ClusterNode('Test Cluster 1', $this->rubricData));

        $clusterNode2 = new ClusterNode('Test Cluster 2', $this->rubricData);
        $clusterNode2->setId(2);
        $this->rootNode->addChild($clusterNode2);

        $clusterNode3 = new ClusterNode('Test Cluster 3', $this->rubricData);
        $clusterNode3->setId(3);
        $this->rootNode->addChild($clusterNode3);

        $categoryNode = new CategoryNode('Test Category 1', $this->rubricData);
        $categoryNode->setId(4);
        $clusterNode2->addChild($categoryNode);

        $categoryNode2 = new CategoryNode('Test Category 2', $this->rubricData);
        $categoryNode2->setId(5);
        $clusterNode2->addChild($categoryNode2);

        $criteriumNode = new CriteriumNode('Test Criterium 1', $this->rubricData);
        $criteriumNode->setId(6);
        $categoryNode->addChild($criteriumNode);

        $level = new Level($this->rubricData);
        $level->setId(75);

        $choice = new Choice($this->rubricData);
        $choice->setId(49);

        $counter = 50;
        foreach($this->rubricData->getChoices() as $choice)
        {
            if(empty($choice->getId()))
            {
                $choice->setId($counter);
            }

            $counter++;
        }

        $this->rubricData->addLevel($level);
        $this->rubricData->addChoice($choice);

        $criteriumNode->addChoice($choice);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->rubricValidator);
        unset($this->rootNode);
        unset($this->rubricData);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    public function testValidator()
    {
        $this->assertEmpty($this->rubricValidator->validateRubric($this->rubricData));
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testValidatorWithDoubleSortValues()
    {
        $clusterNode3 = $this->rootNode->getChildren()[2];
        $clusterNode3->setSort(2);

        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testValidatorWithGapSortValues()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $clusterNode2->setSort(3);

        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \Exception
     */
    public function testValidatorOutOfRange()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $clusterNode2->setSort(0);

        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \Exception
     */
    public function testValidatorOutOfRangeUpperLimit()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $clusterNode2->setSort(4);

        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testValidatorWithWrongDepth()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $clusterNode2->setDepth(2);

        $this->rubricValidator->validateRubric($this->rubricData);
    }
//
//    /**
//     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
//     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
//     */
//    public function testTreeNodeWithWrongRubricData()
//    {
//        $this->rootNode->getChildren()[1]->setRubricData(new RubricData('test'));
//        $this->rubricValidator->validateRubric($this->rubricData);
//    }

//    /**
//     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
//     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
//     */
//    public function testChoiceWithWrongRubricData()
//    {
//        $newRubricData = new RubricData('test');
//        $newRubricData->setId(20);
//        $this->rubricData->getChoices()[0]->setRubricData($newRubricData);
//        $this->rubricValidator->validateRubric($this->rubricData);
//    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidParentNodeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testInvalidAddNodeInChildren()
    {
        $cluster = new ClusterNode('test cluster', $this->rubricData);
        $cluster->setSort(4);
        $cluster->setDepth(1);

        $this->rootNode->getChildren()->add($cluster);
        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRootNodeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testInvalidRootNodeAdd()
    {
        $cluster = new ClusterNode('test cluster', $this->rubricData);
        $cluster->setSort(2);
        $cluster->setDepth(0);

        $this->rubricData->getTreeNodes()->add($cluster);
        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testInvalidRubricDataInRootNode()
    {
        $this->rootNode->setRubricData(new RubricData('test'));
        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
     */
    public function testInvalidAddChoice()
    {
        $rubricData = new RubricData('test 2');
        $rubricData->setId(3);

        $choice = new Choice($rubricData);
        $choice->setId(48);

        $this->rubricData->getChoices()->add($choice);
        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     *
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidCriteriumException
     */
    public function testChoiceWithInvalidCriterium()
    {
        $choice = new Choice($this->rubricData);
        $choice->setId(48);

        /** @var CriteriumNode $criteriumNode */
        $criteriumNode = $this->rubricData->getRootNode()->getChildren()[1]->getChildren()[0]->getChildren()[0];

        $criteriumNode->getChoices()->add($choice);
        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
     */
    public function testInvalidAddLevel()
    {
        $rubricData = new RubricData('test 2');
        $rubricData->setId(3);

        $level = new Level($rubricData);
        $level->setId(76);

        $this->rubricData->getLevels()->add($level);
        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     *
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
     */
    public function testInvalidRubricDataInTreeNode()
    {
        $rubricData2 = new RubricData('Test Rubric');
        $rubricData2->setId(4);
        $clusterNode = new ClusterNode('test cluster 5', $rubricData2);
        $clusterNode->setId(84);

//        $rubricData2->getRootNode()->addChild($clusterNode);

        $this->rubricData->getTreeNodes()->add($clusterNode);
        $this->rubricValidator->validateRubric($this->rubricData);

    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     *
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException
     */
    public function testInvalidRubricDataInTreeNodeChildren()
    {
        $rubricData2 = new RubricData('Test Rubric');
        $rubricData2->setId(4);

        $clusterNode = new ClusterNode('test cluster 5', $rubricData2);
        $clusterNode->setId(84);

        $clusterNode->setSort(4);
        $clusterNode->setDepth(1);

        $this->rootNode->getChildren()->add($clusterNode);
        $this->rubricValidator->validateRubric($this->rubricData);

    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     */
    public function testInvalidChildType()
    {
        $rubricNode2 = new RubricNode('test', $this->rubricData);
        $rubricNode2->setId(7);
        $rubricNode2->setDepth(1);
        $rubricNode2->setSort(4);

        $this->rootNode->getChildren()->add($rubricNode2);
        $this->rubricValidator->validateRubric($this->rubricData);
    }
}

