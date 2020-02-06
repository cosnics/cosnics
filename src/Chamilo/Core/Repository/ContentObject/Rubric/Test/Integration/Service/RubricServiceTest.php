<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Integration\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricTreeBuilder;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricValidator;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
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
                'RubricData', 'TreeNode', 'RubricNode', 'ClusterNode', 'CategoryNode', 'CriteriumNode', 'Level',
                'Choice'
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

        $rubricValidator = new RubricValidator();
        $rubricTreeBuilder = new RubricTreeBuilder($rubricDataRepository);

        $this->rubricService = new RubricService($rubricDataRepository, $rubricValidator, $rubricTreeBuilder);
    }

    protected function tearDown()
    {
//        parent::tearDown();
        unset($this->rubricService);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetTree()
    {
        $this->createTestData();
        $this->getTestEntityManager()->clear();

        $rubricData = $this->rubricService->getRubric(1, 1);

        $this->assertInstanceOf(RubricData::class, $rubricData);

        $this->assertEquals(2, count($rubricData->getRootNode()->getChildren()));
        $this->assertEquals(1, count($rubricData->getRootNode()->getChildren()[1]->getChildren()));
        $this->assertEquals(1, count($rubricData->getRootNode()->getChildren()[1]->getChildren()[0]->getChildren()));
    }

    /**
     * @expectedException \Doctrine\ORM\OptimisticLockException
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetTreeWillLockOptimistically()
    {
        $this->createTestData();

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
        $rubricData = $this->createTestData();

        $this->getTestEntityManager()
            ->createQuery(
                sprintf(
                    'UPDATE %s rd SET rd.version = rd.version + 1 WHERE rd.id=:id',
                    RubricData::class
                )
            )
            ->setParameter('id', $rubricData->getId())
            ->execute();

        $rubricData->getRootNode()->setTitle('New Title');
        $this->rubricService->saveRubric($rubricData);
    }

    /**
     * @return RubricData
     *
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

        $rootNode->addChild($clusterNode);
        $rootNode->addChild($clusterNode2);
        $clusterNode2->addChild($categoryNode);
        $categoryNode->addChild($criteriumNode);

        $this->rubricService->saveRubric($rubricData);

        return $rubricData;
    }
}
