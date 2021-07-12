<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component\AjaxComponent;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceManager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\UserEntityService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_LOAD_ENTITIES = 'LoadEntities';
    const ACTION_SAVE_SCORE = 'SaveScore';
    const ACTION_SAVE_PRESENCE_STATUS = 'SavePresenceStatus';
    const ACTION_LOAD_FEEDBACK = 'LoadFeedback';
    const ACTION_CREATE_FEEDBACK = 'SaveNewFeedback';
    const ACTION_PROCESS_CURIOS_CSV = 'ProcessCuriosCSV';
    const ACTION_IMPORT = 'Import';
    const ACTION_SAVE_OPEN_FOR_STUDENTS = 'SaveOpenForStudents';
    const ACTION_SAVE_SELF_EVALUATION_ALLOWED = 'SaveSelfEvaluationAllowed';

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
     * @return Evaluation
     * @throws UserException
     */
    protected function getEvaluation(): Evaluation
    {
        $evaluation = $this->get_root_content_object();

        if (!$evaluation instanceof Evaluation)
        {
            $this->throwUserException('EvaluationNotFound');
        }

        return $evaluation;
    }

    /**
     * @throws UserException
     */
    protected function initializeEntry(): void
    {
        $evaluation = $this->getEvaluation();
        $entityId = $this->getRequest()->getFromPostOrUrl('entity_id');
        $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $evaluationEntry = $this->getEvaluationEntryService()->createEvaluationEntryIfNotExists($evaluation->getId(), $contextIdentifier, $entityType, $entityId);
        if (!$evaluationEntry)
        {
            $this->throwUserException('NoEvaluationEntry');
        }
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

    protected function getEntityServiceByType(int $entityType): EvaluationEntityServiceInterface
    {
        return $this->getEvaluationEntityServiceManager()->getEntityServiceByType($entityType);
    }

    protected function getEvaluationEntryService(): EvaluationEntryService
    {
        return $this->getService(EvaluationEntryService::class);
    }

    protected function getEvaluationEntityServiceManager(): EvaluationEntityServiceManager
    {
        return $this->getService(EvaluationEntityServiceManager::class);
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

    /**
     * @return array
     * @throws UserException
     */
    protected function getUsers(): array
    {
        $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
        if ($entityType != 0) {
            throw new UserException('Import functionality is only available for user entities.');
        }

        $entityIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $entityService = $this->getEntityServiceByType($entityType);
        $selectedEntities = $entityService->getEntitiesFromIds($entityIds, $contextIdentifier, EvaluationEntityRetrieveProperties::SCORES(), new FilterParameters());
        return iterator_to_array($selectedEntities);
    }
}