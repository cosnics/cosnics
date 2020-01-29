<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Integration\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
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
    }

    protected function tearDown()
    {
//        parent::tearDown();
        unset($this->rubricService);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateRubric()
    {
        $rubricData = $this->rubricService->createRubric('TestRubric');
        $this->assertInstanceOf(RubricData::class, $rubricData);
    }

    /**
     * @throws \Exception
     */
    public function testAddTreeNode()
    {
        $rubricData = $this->rubricService->createRubric('TestRubric');
        $clusterNode = new ClusterNode('Cluster1');
        $clusterNode2 = new ClusterNode('Cluster2');

        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rubricData->getRootNode());
        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rubricData->getRootNode());

        $this->assertEquals(2, $rubricData->getRootNode()->getChildren()[1]->getSort());
    }

    /**
     * @throws \Exception
     */
    public function testRemoveTreeNode()
    {
        $rubricData = $this->rubricService->createRubric('TestRubric');
        $clusterNode = new ClusterNode('Cluster1');
        $clusterNode2 = new ClusterNode('Cluster2');
        $clusterNode3 = new ClusterNode('Cluster3');

        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rubricData->getRootNode());
        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rubricData->getRootNode());
        $this->rubricService->addTreeNode($rubricData, $clusterNode3, $rubricData->getRootNode());

        $cluster2Node = $rubricData->getRootNode()->getChildren()[1];
        $this->rubricService->removeTreeNode($cluster2Node);

        $this->assertEquals(2, $rubricData->getRootNode()->getChildren()[1]->getSort());
    }

    /**
     * @throws \Exception
     */
    public function testMoveTreeNode()
    {
        $rubricData = $this->rubricService->createRubric('TestRubric');
        $clusterNode = new ClusterNode('Cluster1');
        $clusterNode2 = new ClusterNode('Cluster2');
        $clusterNode3 = new ClusterNode('Cluster3');

        $rootNode = $rubricData->getRootNode();

        $this->rubricService->addTreeNode($rubricData, $clusterNode, $rootNode);
        $this->rubricService->addTreeNode($rubricData, $clusterNode2, $rootNode);
        $this->rubricService->addTreeNode($rubricData, $clusterNode3, $rootNode);

        $cluster2 = $rootNode->getChildren()[1];
        $cluster3 = $rootNode->getChildren()[2];

        $this->rubricService->moveTreeNode($cluster2, $cluster3);

        $this->assertEquals(2, $rootNode->getChildren()[1]->getSort());
    }
}
