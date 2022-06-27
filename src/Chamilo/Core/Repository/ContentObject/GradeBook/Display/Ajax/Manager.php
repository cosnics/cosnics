<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax;

/*use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component\BulkSavePresenceEntriesComponent;*/
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component\AjaxComponent;
/*use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceResultPeriodService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceResultEntryService;*/
/*use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Service\GradeBookService;*/
/*use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceValidationService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\UserService;*/

use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
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
    /*const ACTION_LOAD_PRESENCE = 'LoadPresence';
    const ACTION_UPDATE_PRESENCE = 'UpdatePresence';
    const ACTION_LOAD_REGISTERED_PRESENCE_ENTRY_STATUSES = 'LoadRegisteredPresenceEntryStatuses';
    const ACTION_LOAD_PRESENCE_ENTRIES = 'LoadPresenceEntries';
    const ACTION_SAVE_PRESENCE_ENTRY = 'SavePresenceEntry';
    const ACTION_BULK_SAVE_PRESENCE_ENTRIES = 'BulkSavePresenceEntries';
    const ACTION_CREATE_PRESENCE_PERIOD = 'CreatePresencePeriod';
    const ACTION_UPDATE_PRESENCE_PERIOD = 'UpdatePresencePeriod';
    const ACTION_DELETE_PRESENCE_PERIOD = 'DeletePresencePeriod';
    const ACTION_TOGGLE_PRESENCE_ENTRY_CHECKOUT = 'TogglePresenceEntryCheckout';
    const ACTION_LOAD_STATISTICS = 'LoadStatistics';*/

    const ACTION_LOAD_ALL = 'LoadAll';

    const PARAM_ACTION = 'gradebook_display_ajax_action';

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
}