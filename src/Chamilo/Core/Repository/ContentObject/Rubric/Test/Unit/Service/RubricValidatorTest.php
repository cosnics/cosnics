<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricValidator;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
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

    public function testValidator()
    {
        $this->assertNull($this->rubricValidator->validateRubric($this->rubricData));
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
     */
    public function testValidatorWithDoubleSortValues()
    {
        $clusterNode3 = $this->rootNode->getChildren()[2];
        $clusterNode3->setSort(2);

        $this->rubricValidator->validateRubric($this->rubricData);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
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
     * @expectedException \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException
     */
    public function testValidatorWithWrongDepth()
    {
        $clusterNode2 = $this->rootNode->getChildren()[1];
        $clusterNode2->setDepth(2);

        $this->rubricValidator->validateRubric($this->rubricData);
    }



}

