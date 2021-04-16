<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component\AjaxComponent;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_LOAD_ENTITIES = 'LoadEntities';
    const ACTION_SAVE_SCORE = 'SaveScore';
    const ACTION_LOAD_FEEDBACK = 'LoadFeedback';

    const PARAM_ACTION = 'evaluation_display_ajax_action';

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

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface
     */
    protected function getEvaluationServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(EvaluationServiceBridgeInterface::class);
    }

    protected function getFeedbackServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(FeedbackServiceBridgeInterface::class);
    }

    /**
     * @return ContentObjectRepository
     */
    protected function getContentObjectRepository()
    {
        return $this->getService(ContentObjectRepository::class);
    }

    protected function getEntityService() : EntityService
    {
        return $this->getService(EntityService::class);
    }

    protected function getFilterParametersBuilder() : FilterParametersBuilder
    {
        return $this->getService(FilterParametersBuilder::class);
    }

    /**
     * @throws UserException
     * @throws NotAllowedException
     */
    protected function validateSaveScoreInput()
    {
        $this->ajaxComponent->validateSaveScoreInput();
    }

    /**
     * @throws UserException
     */
    protected function throwUserException(string $key)
    {
        $this->ajaxComponent->throwUserException($key);
    }
}