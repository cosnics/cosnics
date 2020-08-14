<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ResultComponent extends Manager implements DelegateComponent
{

    /**
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    function run()
    {
        $rubricData = new RubricData('Een tweede rubric', true);
        $rubricData->setId(30);
        $rubricData->setVersion(1);
        $rubricData->getRootNode()->setId(40);

        $node1 = new ClusterNode('Cluster', $rubricData);
        $node1->setId(38);

        $node2 = new ClusterNode('Cluster 2', $rubricData);
        $node2->setId(39);

        $node3 = new CategoryNode('Category 1', $rubricData);
        $node3->setId(41);
        $node3->setColor('#FF8800');
        $node1->addChild($node3);

        $node4 = new CriteriumNode('Criterium 1', $rubricData);
        $node4->setId(37);
        $node3->addChild($node4);

        $node5 = new CriteriumNode('Criterium 2', $rubricData);
        $node5->setId(42);
        $node3->addChild($node5);

        $rubricData->getRootNode()->addChild($node1)->addChild($node2);

        $level = new Level($rubricData);
        $level->setId(4);
        $level->setTitle('Good');
        $level->setScore(10);

        $level = new Level($rubricData);
        $level->setId(5);
        $level->setTitle('Medium');
        $level->setScore(6);

        $level = new Level($rubricData);
        $level->setId(6);
        $level->setTitle('Bad');
        $level->setScore(2);

        return $this->getTwig()->render(
            'Chamilo\Core\Repository\ContentObject\Rubric:RubricResult.html.twig',
            [
                'RUBRIC_DATA_JSON' => $this->getSerializer()->serialize($rubricData, 'json'),
                'RUBRIC_RESULTS_JSON' => '[{"user":{"id":2,"name":"Sonia","role":"teacher"},"target_user":{"id":5,"name":"joske","role":"student"},"results":[{"tree_node_id":37,"level_id":4,"comment":"Dit is mijn feedback","score":10},{"tree_node_id":42,"level_id":5,"comment":"Nog meer feedback","score":6},{"tree_node_id":41,"level_id":null,"comment":null,"score":16},{"tree_node_id":38,"level_id":null,"comment":null,"score":16},{"tree_node_id":40,"level_id":null,"comment":null,"score":16}]}]'
            ]
        );
    }
}
