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
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_LOAD_ENTITIES = 'LoadEntities';
    const ACTION_SAVE_SCORE = 'SaveScore';
    const ACTION_SAVE_PRESENCE_STATUS = 'SavePresenceStatus';
    const ACTION_LOAD_FEEDBACK = 'LoadFeedback';
    const ACTION_CREATE_FEEDBACK = 'SaveNewFeedback';

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

    protected function initializeEntry(): void
    {
        $entityId = $this->getRequest()->getFromPostOrUrl('entity_id');
        $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $evaluationEntry = $this->getEntityService()->getEvaluationEntryForEntity($contextIdentifier, $entityType, $entityId);
        $this->getFeedbackServiceBridge()->setEntryId($evaluationEntry->getId());
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
    protected function validateEvaluationEntityInput()
    {
        $this->ajaxComponent->validateEvaluationEntityInput();
    }

    /**
     * @throws UserException
     */
    protected function throwUserException(string $key)
    {
        $this->ajaxComponent->throwUserException($key);
    }

    /**
     *
     * @param int $date
     *
     * @return string
     */
    protected function format_date($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        return DatetimeUtilities::format_locale_date($date_format, $date);
    }

}