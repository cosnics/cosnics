<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component\AjaxComponent;

use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookAjaxService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Response\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_LOAD_GRADEBOOK_DATA = 'LoadGradeBookData';
    const ACTION_UPDATE_CATEGORY = 'UpdateCategory';

    const PARAM_ACTION = 'gradebook_display_ajax_action';

    const PARAM_GRADEBOOK_DATA_ID = 'gradebookDataId';
    const PARAM_VERSION = 'version';
    const PARAM_CATEGORY_DATA = 'categoryData';

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
                'The ajax components from the gradebook display manager can only be called from ' .
                'within the AjaxComponent of the gradebook display application'
            );
        }

        $this->ajaxComponent = $applicationConfiguration->getApplication();

        parent::__construct($applicationConfiguration);
    }

    /**
     * @return string|Response
     */
    function run()
    {
        try
        {
            $result = $this->runAjaxComponent();

            return new JsonResponse($this->serialize($result, 'json'), 200, [], true);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            return new AjaxExceptionResponse($ex);
        }
    }

    /**
     * @return array
     */
    abstract function runAjaxComponent();

    /**
     * @return bool
     */
    protected function canUserEditGradeBook(): bool
    {
        return $this->ajaxComponent->getRightsService()->canUserEditGradeBook();
    }

    /**
     * @return bool
     */
    protected function canUserViewGradeBook(): bool
    {
        return $this->ajaxComponent->getRightsService()->canUserEditGradeBook($this->getUser());
    }

    /**
     * @return GradeBookServiceBridgeInterface
     */
    protected function getGradeBookServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(GradeBookServiceBridgeInterface::class);
    }

/*    /**
     * @return UserService
     */
/*    protected function getUserService(): UserService
    {
        return $this->getService(UserService::class);
    }*/

/*    /**
     * @return PresenceService
     */
/*    protected function getPresenceService(): PresenceService
    {
        return $this->getService(PresenceService::class);
    }*/

/*    /**
     * @return PresenceResultPeriodService
     */
/*    protected function getPresenceResultPeriodService(): PresenceResultPeriodService
    {
        return $this->getService(PresenceResultPeriodService::class);
    }*/

/*    /**
     * @return PresenceResultEntryService
     */
/*    protected function getPresenceResultEntryService(): PresenceResultEntryService
    {
        return $this->getService(PresenceResultEntryService::class);
    }*/

/*    /**
     * @return PresenceValidationService
     */
/*    protected function getPresenceValidationService(): PresenceValidationService
    {
        return $this->getService(PresenceValidationService::class);
    }*/

    /**
     * @param string $json
     * @return array
     */
    protected function deserialize(string $json): array
    {
        return $this->getSerializer()->deserialize($json, 'array', 'json');
    }

    protected function serialize(array $array): string
    {
        $serializer =
            SerializerBuilder::create()
                ->setSerializationContextFactory(function () {
                    return SerializationContext::create()
                        ->setSerializeNull(true);
                })
                ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
                ->build();
        return $serializer->serialize($array, 'json');
    }

    protected function get_root_content_object()
    {
        return $this->get_application()->get_root_content_object();
    }

    /**
     * @return GradeBookService
     */
    protected function getGradeBookService()
    {
        return $this->getService(GradeBookService::class);
    }

    /**
     * @return GradeBookAjaxService
     */
    protected function getGradeBookAjaxService()
    {
        return $this->getService(GradeBookAjaxService::class);
    }

    /**
     * @return GradeBook
     * @throws UserException
     */
    protected function getGradeBook(): GradeBook
    {
        $gradebook = $this->get_root_content_object();

        if (!$gradebook instanceof GradeBook)
        {
            $this->throwUserException('GradeBookNotFound');
        }

        return $gradebook;
    }

    protected function getFilterParametersBuilder() : FilterParametersBuilder
    {
        return $this->getService(FilterParametersBuilder::class);
    }

/*    /**
     * @throws UserException
     * @throws NotAllowedException
     */
/*    protected function validatePresenceUserInput()
    {
        $this->ajaxComponent->validatePresenceUserInput();
    }*/

    /**
     * @throws UserException
     */
    protected function throwUserException(string $key)
    {
        $this->ajaxComponent->throwUserException($key);
    }

/*    /**
     * @throws NotAllowedException
     * @throws UserException
     */
/*    protected function validatePresenceResultEntryInput()
    {
        $this->validatePresenceUserInput();

        $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
        $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
        $period = $this->getPresenceResultPeriodService()->findResultPeriodForPresence($this->getPresence(), $periodId, $contextIdentifier);
        if (empty($period)) {
            $this->throwUserException('PresenceResultPeriodNotFound');
        }

        $statusId = $this->getRequest()->getFromPostOrUrl('status_id');
        if (!$this->getPresenceService()->isValidStatusId($this->getPresence(), $statusId)) {
            $this->throwUserException('InvalidStatus');
        }
    }*/

    /**
     * @return int
     */
    protected function getGradeBookDataId()
    {
        return $this->getRequest()->getFromPost(self::PARAM_GRADEBOOK_DATA_ID);
    }

    /**
     * @return int
     */
    protected function getVersion()
    {
        return $this->getRequest()->getFromPost(self::PARAM_VERSION);
    }

    /**
     * @return string
     */
    protected function getCategoryData()
    {
        return $this->getRequest()->getFromPost(self::PARAM_CATEGORY_DATA);
    }

}