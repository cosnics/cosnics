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
        $rubric = $this->getRubric();
        $rubricData = $this->getRubricService()->getRubric($rubric->getActiveRubricDataId());

        echo '<pre>';
        print_r($this->getSerializer()->serialize($rubricData, 'json'));
        echo '</pre>';

        return $this->getTwig()->render(
            'Chamilo\Core\Repository\ContentObject\Rubric:RubricBuilder.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                'RUBRIC_DATA_JSON' => $this->getSerializer()->serialize($rubricData, 'json'),
                'ADD_LEVEL_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_ADD_LEVEL
                    ]
                ),
                'ADD_TREE_NODE_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_ADD_TREE_NODE
                    ]
                ),
                'DELETE_LEVEL_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_DELETE_LEVEL
                    ]
                ),
                'DELETE_TREE_NODE_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_DELETE_TREE_NODE
                    ]
                ),
                'MOVE_LEVEL_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_MOVE_LEVEL
                    ]
                ),
                'MOVE_TREE_NODE_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_MOVE_TREE_NODE
                    ]
                ),
                'UPDATE_CHOICE_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_UPDATE_CHOICE
                    ]
                ),
                'UPDATE_LEVEL_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_UPDATE_LEVEL
                    ]
                ),
                'UPDATE_TREE_NODE_AJAX_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::ACTION_UPDATE_TREE_NODE
                    ]
                ),
            ]
        );
    }
}
