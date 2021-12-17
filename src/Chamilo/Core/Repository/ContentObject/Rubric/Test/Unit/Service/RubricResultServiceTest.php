<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricResultService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests the RubricResultService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricResultServiceTest extends ChamiloTestCase
{
    /**
     * @var RubricResultService
     */
    protected $rubricResultService;

    /**
     * @var RubricResultRepository | PHPUnit_Framework_MockObject_MockObject
     */
    protected $rubricResultRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->rubricResultRepositoryMock = $this->getMockBuilder(RubricResultRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->rubricResultService = new RubricResultService($this->rubricResultRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->rubricResultService);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function testResultCalculation()
    {
        $rubricData = new RubricData('My result rubric');
        $rubricData->setUseScores(true);
        $rubricData->setUseRelativeWeights(false);

        $rubricData->getRootNode()->setId(1);

        $clusterNode1 = new ClusterNode('Cluster 1', $rubricData);
        $clusterNode1->setId(2);

        $clusterNode2 = new ClusterNode('Cluster 2', $rubricData);
        $clusterNode2->setId(3);

        $categoryNode1 = new CategoryNode('Category 1', $rubricData);
        $categoryNode1->setId(4);

        $categoryNode2 = new CategoryNode('Category 2', $rubricData);
        $categoryNode2->setId(5);

        $criteriumNode1 = new CriteriumNode('Criterium 1', $rubricData);
        $criteriumNode1->setId(6);

        $criteriumNode2 = new CriteriumNode('Criterium 2', $rubricData);
        $criteriumNode2->setId(7);

        $criteriumNode3 = new CriteriumNode('Criterium 3', $rubricData);
        $criteriumNode3->setId(8);

        $criteriumNode4 = new CriteriumNode('Criterium 4', $rubricData);
        $criteriumNode4->setId(9);
        $criteriumNode4->setWeight(80);

        $criteriumNode5 = new CriteriumNode('Criterium 5', $rubricData);
        $criteriumNode5->setId(10);

        $rubricData->getRootNode()->addChild($clusterNode1);
        $rubricData->getRootNode()->addChild($clusterNode2);

        $clusterNode1->addChild($criteriumNode1);
        $clusterNode1->addChild($criteriumNode2);

        $clusterNode2->addChild($categoryNode1);
        $clusterNode2->addChild($categoryNode2);

        $categoryNode1->addChild($criteriumNode3);
        $categoryNode2->addChild($criteriumNode4);
        $categoryNode2->addChild($criteriumNode5);

        $level1 = new Level($rubricData);
        $level1->setId(1);
        $level1->setTitle('Good');
        $level1->setScore(1);

        $level2 = new Level($rubricData);
        $level2->setId(2);
        $level2->setTitle('Bad');
        $level2->setScore(0);

        $counter = 1;

        foreach ($rubricData->getChoices() as $choice)
        {
            $choice->setId($counter);
            $counter ++;
        }

        /** @var Choice $firstChoice */
        $firstChoice = $criteriumNode5->getChoices()->first();
        $firstChoice->setHasFixedScore(true);
        $firstChoice->setFixedScore(5);

        // We catch the results in an array and we normalize the data so we can test it easily
        $resultsArray = [];

        $this->rubricResultRepositoryMock->expects($this->exactly(10))
            ->method('saveRubricResult')
            ->will(
                $this->returnCallback(
                    function (RubricResult $rubricResult) use (&$resultsArray) {
                        $resultsArray[$rubricResult->getTreeNode()->getId()] = $rubricResult->getScore();

                        return true;
                    }
                )
            );

        $resultJSONModels = [
            new TreeNodeResultJSONModel(6, 1),
            new TreeNodeResultJSONModel(7, 2),
            new TreeNodeResultJSONModel(8, 2),
            new TreeNodeResultJSONModel(9, 1),
            new TreeNodeResultJSONModel(10, 1)
        ];

        $user = new User();
        $user->setId(20);

        $contextIdentifier =
            new ContextIdentifier('Chamilo\\Application\\Weblcms\\Bridge\\Assignment\\Storage\\Entry', 20);

        $this->rubricResultService->storeRubricResults(
            $user, [$user], $rubricData, $contextIdentifier, $resultJSONModels
        );

        $this->assertEquals(6.8, $resultsArray[1]); // Rubric
        $this->assertEquals(1, $resultsArray[2]);   // Cluster 1
        $this->assertEquals(5.8, $resultsArray[3]); // Cluster 2
        $this->assertEquals(0, $resultsArray[4]);   // Category 1
        $this->assertEquals(5.8, $resultsArray[5]); // Category 2
        $this->assertEquals(1, $resultsArray[6]);   // Criterium 1
        $this->assertEquals(0, $resultsArray[7]);   // Criterium 2
        $this->assertEquals(0, $resultsArray[8]);   // Criterium 3
        $this->assertEquals(0.8, $resultsArray[9]); // Criterium 4
        $this->assertEquals(5, $resultsArray[10]);  // Criterium 5
    }

}


