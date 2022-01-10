<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Integration\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricResultService;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricTreeBuilder;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricValidator;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\DoctrineORMFixturesBasedTestCase;
use function sprintf;

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
     * @var RubricData
     */
    protected $rubricData;

    /**
     * @inheritDoc
     */
    protected function getStorageUnitsToCreate()
    {
        return [
            'Chamilo\Core\Repository\ContentObject\Rubric' => [
                'RubricData', 'TreeNode', 'RubricNode', 'ClusterNode', 'CategoryNode', 'CriteriumNode', 'Level',
                'Choice', 'RubricResult'
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

        /** @var RubricResultRepository $rubricResultRepository */
        $rubricResultRepository = $this->getTestEntityManager()->getRepository(RubricResult::class);

        $rubricValidator = new RubricValidator();
        $rubricTreeBuilder = new RubricTreeBuilder($rubricDataRepository);
        $rubricResultService = new RubricResultService($rubricResultRepository);

        $this->rubricService =
            new RubricService($rubricDataRepository, $rubricValidator, $rubricTreeBuilder, $rubricResultService);

        $this->createTestData();
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createTestData()
    {
        $rubricData = new RubricData('TestRubric');

        $rootNode = $rubricData->getRootNode();

        $clusterNode = new ClusterNode('Cluster1', $rubricData);
        $clusterNode2 = new ClusterNode('Cluster2', $rubricData);
        $categoryNode = new CategoryNode('Category1', $rubricData);
        $criteriumNode = new CriteriumNode('Criterium1', $rubricData);
        $criteriumNode2 = new CriteriumNode('Criterium2', $rubricData);

        $rootNode->addChild($clusterNode);
        $rootNode->addChild($clusterNode2);
        $rootNode->addChild($criteriumNode2);

        $clusterNode2->addChild($categoryNode);
        $categoryNode->addChild($criteriumNode);

        $level1 = new Level($rubricData);
        $level1->setTitle('Level 1');
        $level2 = new Level($rubricData);
        $level2->setTitle('Level 2');


        $this->rubricService->saveRubric($rubricData);

        $this->rubricData = $rubricData;
    }

    protected function tearDown()
    {
//        parent::tearDown();
        unset($this->rubricService);
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetTree()
    {
        $this->getTestEntityManager()->clear();

        $rubricData = $this->rubricService->getRubric(1, 1);

        $this->assertInstanceOf(RubricData::class, $rubricData);

        $this->assertEquals(3, count($rubricData->getRootNode()->getChildren()));
        $this->assertEquals(1, count($rubricData->getRootNode()->getChildren()[1]->getChildren()));
        $this->assertEquals(1, count($rubricData->getRootNode()->getChildren()[1]->getChildren()[0]->getChildren()));
    }

    /**
     * @expectedException \Doctrine\ORM\OptimisticLockException
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetTreeWillLockOptimistically()
    {
        $this->rubricService->getRubric(1, 2);
    }

    /**
     * @expectedException \Doctrine\ORM\OptimisticLockException
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testSaveRubricWillLockOptimistically()
    {
        $this->getTestEntityManager()
            ->createQuery(
                sprintf(
                    'UPDATE %s rd SET rd.version = rd.version + 1 WHERE rd.id=:id',
                    RubricData::class
                )
            )
            ->setParameter('id', $this->rubricData->getId())
            ->execute();

        $this->rubricData->getRootNode()->setTitle('New Title');
        $this->rubricService->saveRubric($this->rubricData);
    }

    public function testDeleteTreeNodeDeletesChildrenAndChoices()
    {
        $treeNode = $this->rubricData->getTreeNodeById(5);
        $this->rubricData->removeTreeNode($treeNode);
        $this->rubricService->saveRubric($this->rubricData);

        $countTreeNodes = $this->getTestEntityManager()
            ->createQueryBuilder()
            ->select('count(tn.id)')
            ->from(TreeNode::class, 'tn')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(3, $countTreeNodes);

        $countChoices = $this->getTestEntityManager()
            ->createQueryBuilder()
            ->select('count(ch.id)')
            ->from(Choice::class, 'ch')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(2, $countChoices);

    }

    public function testDeleteLevelDeletesChoicesForLevel()
    {
        $this->getTestEntityManager()->clear();

        $rubricData = $this->rubricService->getRubric(1);

        $level = $rubricData->getLevelById(1);
        $rubricData->removeLevel($level);
        $this->rubricService->saveRubric($rubricData);

        $countChoices = $this->getTestEntityManager()
            ->createQueryBuilder()
            ->select('count(ch.id)')
            ->from(Choice::class, 'ch')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(2, $countChoices);
    }
}
