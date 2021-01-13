<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricResultJSONGenerator;
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
        $rubric = $this->getRubric();
        $rubricData = $this->getRubricService()->getRubric($rubric->getActiveRubricDataId());

//        $targetUsers = $this->getRubricBridge()->getTargetUsers();
//        $targetUser = $targetUsers[0];

        $results = $this->getRubricResultJSONGenerator()->generateRubricResultsJSON(
            $rubricData, $this->getRubricBridge()->getContextIdentifier()
        );

        return $this->getTwig()->render(
            'Chamilo\Core\Repository\ContentObject\Rubric:RubricResult.html.twig',
            [
                'LANGUAGE' => $this->getTranslator()->getLocale(),
                'RUBRIC_DATA_JSON' => $this->getSerializer()->serialize($rubricData, 'json'),
                'RUBRIC_RESULTS_JSON' => $this->getSerializer()->serialize($results, 'json')
            ]
        );
    }

    /**
     * @return RubricResultJSONGenerator
     */
    protected function getRubricResultJSONGenerator()
    {
        return $this->getService(RubricResultJSONGenerator::class);
    }
}
