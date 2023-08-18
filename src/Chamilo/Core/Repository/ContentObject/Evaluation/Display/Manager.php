<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceManager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\RightsService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\FeedbackRightsServiceBridge;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\FeedbackServiceBridge;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationRubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge\RubricBridgeInterface;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\RubricBridge;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_ACTION = 'evaluation_display_action';
    const PARAM_ENTITY_ID = 'entity_id';

    const DEFAULT_ACTION = 'Browser';

    const ACTION_AJAX = 'Ajax';
    const ACTION_PUBLISH_RUBRIC = 'PublishRubric';
    const ACTION_BUILD_RUBRIC = 'BuildRubric';
    const ACTION_REMOVE_RUBRIC = 'RemoveRubric';
    const ACTION_IMPORT_FROM_CURIOS = 'ImportFromCurios';
    const ACTION_EXTENSION = 'Extension';
    const ACTION_EXPORT = 'Export';

    const ACTION_ENTRY = 'Entry';
    const ACTION_SAVE_SCORE = 'SaveScore';
    const ACTION_BROWSER = 'Browser';

    const SESSION_RUBRIC_SCORE = 'RUBRIC_SCORE';

    const EVALUATION_URL = 'evaluation_url';
    const IMPORT_RESULTS_URL = 'import_results_url';

    /**
     *
     * @var integer
     */
    protected $entityType;

    /**
     *
     * @var integer
     */
    protected $entityIdentifier;

    /**
     * @var RightsService
     */
    protected $rightsService;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->buildBridgeServices();
    }

    protected function ensureEntityIdentifier()
    {
        $entityIdentifier = $this->getEntityIdentifier();
        if ($entityIdentifier)
        {
            $this->set_parameter(self::PARAM_ENTITY_ID, $entityIdentifier);
        }
    }

    /**
     * @return RightsService
     */
    public function getRightsService()
    {
        if (!isset($this->rightsService))
        {
            $this->rightsService = new RightsService();
            $this->rightsService->setEvaluationServiceBridge($this->getEvaluationServiceBridge());
        }

        return $this->rightsService;
    }

    /**
     *
     * @return integer
     */
    public function getEntityType()
    {
        if (!isset($this->entityType))
        {
            $this->entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
        }

        return $this->entityType;
    }

    /**
     * @return mixed
     */
    protected function getEntityIdentifier() {
        if (!isset($this->entityIdentifier))
        {
            $this->entityIdentifier = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTITY_ID);

            if (empty($this->entityIdentifier))
            {
                $this->entityIdentifier =
                    $this->getEvaluationServiceBridge()->getCurrentEntityIdentifier($this->getUser());
            }
        }

        if (empty($this->entityIdentifier))
        {
            throw new UserException($this->getTranslator()->trans('CanNotViewEvaluation', [], Manager::context()));
        }

        return $this->entityIdentifier;

    }

    /**
     * Builds the bridge services for the feedback and for the extensions
     */
    protected function buildBridgeServices()
    {
        $rubricBridge = new RubricBridge($this->getEvaluationServiceBridge(), $this->getEvaluationEntryService());
        $feedbackRightsServiceBridge = new FeedbackRightsServiceBridge($this->getEvaluationServiceBridge(), $this->getRightsService());
        $feedbackRightsServiceBridge->setCurrentUser($this->getUser());

        $feedbackServiceBridge = $this->getService(FeedbackServiceBridge::class);
        $this->getBridgeManager()->addBridge($feedbackServiceBridge);
        $this->getBridgeManager()->addBridge($feedbackRightsServiceBridge);
        $this->getBridgeManager()->addBridge($rubricBridge);
    }

    protected function getFeedbackServiceBridge(): FeedbackServiceBridge
    {
        return $this->getBridgeManager()->getBridgeByInterface(FeedbackServiceBridgeInterface::class);
    }

    protected function getFeedbackRightsServiceBridge(): FeedbackRightsServiceBridge
    {
        return $this->getBridgeManager()->getBridgeByInterface(FeedbackRightsServiceBridgeInterface::class);
    }

    /**
     * @return bool
     */
    protected function supportsRubrics()
    {
        return $this->getRegistrationConsulter()->isContextRegistered(
            'Chamilo\\Core\\Repository\\ContentObject\\Rubric'
        );
    }

    /**
     * @return bool
     */
    protected function supportsAns()
    {
        return $this->getRegistrationConsulter()->isContextRegistered(
            'Hogent\\Extension\\Chamilo\\Core\\Repository\\ContentObject\\Evaluation\\Extension\\Ans'
        );
        /*if (empty($this->course))
        {
            return false;
        }
        $toolRegistrationId = $this->courseService->getToolRegistration('Ans')->getId();
        return $this->courseSettingsController->get_course_setting(
            $this->course,
            CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
            $toolRegistrationId);*/
    }

    /**
     * @return ContentObjectService
     */
    protected function getContentObjectService()
    {
        return $this->getService(ContentObjectService::class);
    }

    /**
     * @param string $action
     *
     * @param bool $embedded
     *
     * @return string|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws UserException
     */
    protected function runRubricComponent(string $action, bool $embedded = true)
    {
        $rubricId = $this->getEvaluation()->getRubricId();

        if (!$rubricId)
        {
            return '';
        }

        try
        {
            $rubric = $this->getContentObjectService()->findById($rubricId);
        }
        catch (\TypeError | \Exception $e)
        {
            return false;
        }

        if (!$rubric instanceof Rubric)
        {
            return '';
        }

        $applicationConfiguration =
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this, [], $embedded);

        $applicationConfiguration->set(
            \Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager::PARAM_RUBRIC_CONTENT_OBJECT, $rubric
        );

        $application =
            $this->getApplicationFactory()->getApplication(
                'Chamilo\Core\Repository\ContentObject\Rubric\Display', $applicationConfiguration, $action
            );

        $response = $application->run();

        if ($embedded && ($response instanceof JsonResponse || $response instanceof RedirectResponse))
        {
            $response->send();
            exit;
        }

        return $response;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface
     */
    protected function getEvaluationServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(EvaluationServiceBridgeInterface::class);
    }

    /**
     * @return RubricBridge
     */
    protected function getRubricBridge(): RubricBridge
    {
        return $this->getBridgeManager()->getBridgeByInterface(RubricBridgeInterface::class);
    }

    /**
     * @return EvaluationEntityServiceInterface
     */
    protected function getEntityService(): EvaluationEntityServiceInterface
    {
        /** @var EvaluationEntityServiceManager $evaluationEntityServiceManager */
        $evaluationEntityServiceManager = $this->getService(EvaluationEntityServiceManager::class);
        return $evaluationEntityServiceManager->getEntityServiceByType($this->getEntityType());
    }

    /**
     * @return EvaluationEntryService
     */
    protected function getEvaluationEntryService()
    {
        return $this->getService(EvaluationEntryService::class);
    }

    /**
     * @return EvaluationRubricService
     */
    protected function getEvaluationRubricService()
    {
        return $this->getService(EvaluationRubricService::class);
    }

    /**
     * @return RubricService
     */
    protected function getRubricService()
    {
        return $this->getService(RubricService::class);
    }

    /**
     * @return Evaluation
     * @throws UserException
     */
    public function getEvaluation(): Evaluation
    {
        $evaluation = $this->get_root_content_object();

        if (!$evaluation instanceof Evaluation)
        {
            $this->throwUserException('EvaluationNotFound');
        }

        return $evaluation;
    }

    /**
     * @param string[] $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getAvailableEntitiesParameters($parameters)
    {
        $availableEntities = [];

        $availableEntityIds =
            $this->getEvaluationServiceBridge()->getAvailableEntityIdentifiersForUser($this->getUser());

        foreach ($availableEntityIds as $availableEntityId)
        {
            if ($availableEntityId == $this->getEntityIdentifier())
            {
                continue;
            }

            $availableEntities[$availableEntityId] =
                $this->getEvaluationServiceBridge()->renderEntityNameByEntityTypeAndEntityId(
                    $this->getEntityType(), $availableEntityId
                );
        }

        $parameters['HAS_MULTIPLE_ENTITIES'] = count($availableEntityIds) > 1;
        $parameters['AVAILABLE_ENTITIES'] = $availableEntities;

        $parameters['ENTITY_NAME'] = $this->getEvaluationServiceBridge()->renderEntityNameByEntityTypeAndEntityId(
            $this->getEntityType(), $this->getEntityIdentifier()
        );

        $parameters['ENTITY_TYPE_PLURAL'] =
            strtolower($this->getEvaluationServiceBridge()->getPluralEntityNameByType($this->getEntityType()));

        $parameters['ENTITY_TYPE_SINGLE'] =
            strtolower($this->getEvaluationServiceBridge()->getEntityNameByType($this->getEntityType()));

        return $parameters;
    }

    /**
     * @throws NotAllowedException
     * @throws UserException
     */
    public function validateEvaluationEntityInput()
    {
        $this->validateIsPostRequest();
        $this->validateIsEvaluation();
        $this->validateEntity();
    }

    /**
     * @throws NotAllowedException
     */
    protected function validateIsPostRequest()
    {
        if (!$this->getRequest()->isMethod('POST'))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @throws UserException
     */
    protected function validateIsEvaluation()
    {
        $evaluation = $this->get_root_content_object();

        if (! $evaluation instanceof Evaluation)
        {
            $this->throwUserException('EvaluationNotFound');
        }
    }

    /**
     * @throws UserException
     */
    protected function validateEntity()
    {
        $entityId = $this->getEntityIdentifier();

        if (empty($entityId))
        {
            $this->throwUserException('EntityIdNotProvided');
        }

        $userIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();

        if (! in_array($entityId, $userIds))
        {
            $this->throwUserException('EntityNotInList');
        }
    }

    /**
     * @param string $key
     * @throws UserException
     */
    public function throwUserException($key = "")
    {
        throw new UserException(
            $this->getTranslator()->trans($key, [], Manager::context())
        );
    }

}
