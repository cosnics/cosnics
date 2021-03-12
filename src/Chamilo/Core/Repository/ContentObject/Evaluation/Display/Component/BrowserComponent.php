<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;


/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        $this->checkAccessRights();

        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context() . ':EntityBrowser.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation())
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return string[]
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     */
    protected function getTemplateProperties()
    {
        $object = $this->get_root_content_object();

        $supportsRubrics = $this->supportsRubrics();
        $hasRubric = false;
        $rubricPreview = null;

        if ($supportsRubrics)
        {
            $hasRubric = $object->getRubricId() != null;
            $rubricPreview = $this->runRubricComponent('Preview');
        }

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'SUPPORTS_RUBRICS' => $this->supportsRubrics(),
            'HAS_RUBRIC' => $hasRubric,
            'ADD_RUBRIC_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_PUBLISH_RUBRIC]),
            'BUILD_RUBRIC_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_BUILD_RUBRIC]),
            'REMOVE_RUBRIC_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_REMOVE_RUBRIC]),
            'CAN_BUILD_RUBRIC' => true, // todo
            'RUBRIC_PREVIEW' => $rubricPreview,
            'LOAD_ENTITIES_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager::ACTION_LOAD_ENTITIES
                ]
            ),
        ];
    }
}