<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Integration\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricTreeBuilder;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\TreeNodeRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\DoctrineORMFixturesBasedTestCase;

/**
 * Class RubricServiceTest
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricServiceTest extends DoctrineORMFixturesBasedTestCase
{
    /**
     * @var RubricService
     */
    protected $rubricService;
    /**
     * @var RubricTreeBuilder
     */
    protected $rubricTreeBuilder;

    /**
     * @inheritDoc
     */
    protected function getStorageUnitsToCreate()
    {
        return [
            'Chamilo\Core\Repository\ContentObject\Rubric' => [
                'RubricData', 'TreeNode', 'RubricNode', 'ClusterNode', 'CategoryNode', 'CriteriumNode', 'Level', 'Choice'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getFixtureFiles()
    {
        return [];
    }

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var RubricDataRepository $rubricDataRepository */
        $rubricDataRepository = $this->getTestEntityManager()->getRepository(RubricData::class);

        /** @var TreeNodeRepository $treeNodeRepository */
        $treeNodeRepository = $this->getTestEntityManager()->getRepository(TreeNode::class);

        $this->rubricService = new RubricService($rubricDataRepository, $treeNodeRepository);
        $this->rubricTreeBuilder = new RubricTreeBuilder($rubricDataRepository);
    }

    protected function tearDown()
    {
//        parent::tearDown();
        unset($this->rubricService);
    }

//    /**
//     * @throws \Doctrine\ORM\ORMException
//     */
//    public function testCreateRubric()
//    {
//        $rubricData = $this->rubricService->createRubric('TestRubric');
//        $this->assertInstanceOf(RubricData::class, $rubricData);
//    }
//
//    /**
//     * @throws \Exception
//     */
//    public function testAddTreeNode()
//    {
//        $rubricData = $this->rubricService->createRubric('TestRubric');
//        $clusterNode = new ClusterNode('Cluster1');
//        $clusterNode2 = new ClusterNode('Cluster2');
//
//        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rubricData->getRootNode());
//        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rubricData->getRootNode());
//
//        $this->assertEquals(2, $rubricData->getRootNode()->getChildren()[1]->getSort());
//    }
//
//    /**
//     * @throws \Exception
//     */
//    public function testRemoveTreeNode()
//    {
//        $rubricData = $this->rubricService->createRubric('TestRubric');
//        $clusterNode = new ClusterNode('Cluster1');
//        $clusterNode2 = new ClusterNode('Cluster2');
//        $clusterNode3 = new ClusterNode('Cluster3');
//
//        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rubricData->getRootNode());
//        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rubricData->getRootNode());
//        $this->rubricService->addTreeNode($rubricData, $clusterNode3, $rubricData->getRootNode());
//
//        $this->rubricService->removeTreeNode($clusterNode2);
//
//        $this->assertEquals(2, $clusterNode3->getSort());
//    }
//
//    /**
//     * @throws \Exception
//     */
//    public function testMoveTreeNode()
//    {
//        $rubricData = $this->rubricService->createRubric('TestRubric');
//        $clusterNode = new ClusterNode('Cluster1');
//        $clusterNode2 = new ClusterNode('Cluster2');
//        $clusterNode3 = new ClusterNode('Cluster3');
//
//        $rootNode = $rubricData->getRootNode();
//
//        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rootNode);
//        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rootNode);
//        $this->rubricService->addTreeNode($rubricData, $clusterNode3, $rootNode);
//
//        $this->rubricService->moveTreeNode($clusterNode2, $clusterNode3);
//
//        $this->assertEquals(2, $clusterNode3->getSort());
//    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function testTreeBuilder()
    {
        $rubricData = $this->rubricService->createRubric('TestRubric');

        $rootNode = $rubricData->getRootNode();

        $clusterNode = new ClusterNode('Cluster1');
        $clusterNode2 = new ClusterNode('Cluster2');
        $categoryNode = new CategoryNode('Category1');
        $criteriumNode = new CriteriumNode('Criterium1');

        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rootNode);
        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rootNode);

        $clusterNode2 = $rootNode->getChildren()[1];

        $this->rubricService->addTreeNode($rubricData, $categoryNode, $clusterNode2);

        $categoryNode = $clusterNode2->getChildren()[0];

        $this->rubricService->addTreeNode($rubricData, $criteriumNode, $categoryNode);
        $this->getTestEntityManager()->clear();

        $rubricData = $this->rubricTreeBuilder->buildRubricTreeByRubricDataId(1);
        $this->assertInstanceOf(RubricData::class, $rubricData);
    }
}
