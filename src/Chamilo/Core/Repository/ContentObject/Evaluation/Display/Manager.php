<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\RubricBridge;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const DEFAULT_ACTION = 'Browser';

    const ACTION_AJAX = 'Ajax';
    const ACTION_PUBLISH_RUBRIC = 'PublishRubric';
    const ACTION_BUILD_RUBRIC = 'BuildRubric';
    const ACTION_REMOVE_RUBRIC = 'RemoveRubric';

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $rubricBridge = new RubricBridge(null);

        $this->getBridgeManager()->addBridge($rubricBridge);
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
     */
    protected function runRubricComponent(string $action, bool $embedded = true)
    {
        $rubricId = $this->get_root_content_object()->getRubricId();

        if (!$rubricId)
        {
            return '';
        }

        $rubric = $this->getContentObjectService()->findById($rubricId);

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

}