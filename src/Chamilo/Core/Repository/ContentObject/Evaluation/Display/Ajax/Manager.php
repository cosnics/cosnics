<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component\AjaxComponent;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_SET_USE_SCORES = 'SetUseScores';
    const ACTION_SET_USE_FEEDBACK = 'SetUseFeedback';

    const PARAM_ACTION = 'evaluation_display_ajax_action';
    const PARAM_USE_SCORES = 'use_scores';
    const PARAM_USE_FEEDBACK = 'use_feedback';

    /**
     * @var AjaxComponent
     */
    protected $ajaxComponent;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (!$applicationConfiguration->getApplication() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'The ajax components from the evaluation display manager can only be called from ' .
                'within the AjaxComponent of the evaluation display application'
            );
        }

        $this->ajaxComponent = $applicationConfiguration->getApplication();

        parent::__construct($applicationConfiguration);
    }

    protected function get_root_content_object()
    {
        return $this->get_application()->get_root_content_object();
    }
}