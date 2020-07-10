<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;

/**
 * Class BuilderComponent
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class BuilderComponent extends Manager implements DelegateComponent
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
        /*$rubric = $this->getRubric();
        $rubricData = $this->getRubricService()->getRubric($rubric->getActiveRubricDataId());*/

        $rubricData = new RubricData('Een tweede rubric', true);
        $rubricData->setId(30);
        $rubricData->setVersion(1);
        $rubricData->getRootNode()->setId(40);

        $node1 = new ClusterNode('test cluster', $rubricData);
        $node1->setId(38);

        $node2 = new ClusterNode('test cluster 2', $rubricData);
        $node2->setId(39);

        $node3 = new CategoryNode('test category 1', $rubricData);
        $node3->setId(41);
        $node3->setColor('#FFFF00');
        $node1->addChild($node3);

        $node4 = new CriteriumNode('test criterium 1', $rubricData);
        $node4->setId(37);
        $node3->addChild($node4);
        $rubricData->getRootNode()->addChild($node1)->addChild($node2);

        $level = new Level($rubricData);
        $level->setId(4);
        $level->setTitle('Good');

        $level = new Level($rubricData);
        $level->setId(5);
        $level->setTitle('Bad');

        echo '<pre>';
        print_r($this->getSerializer()->serialize($rubricData, 'json'));
        echo '</pre>';

        return $this->getTwig()->render(
            'Chamilo\Core\Repository\ContentObject\Rubric:RubricBuilder.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                'RUBRIC_DATA_JSON' => $this->getSerializer()->serialize($rubricData, 'json')
            ]
        );
    }
}
