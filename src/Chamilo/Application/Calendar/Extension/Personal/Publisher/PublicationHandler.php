<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Publisher;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * The publication handler for the personal calendar extension
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationHandler implements PublicationHandlerInterface
{

    /**
     * The publication Form
     *
     * @var PublicationForm
     */
    protected $publicationForm;

    /**
     * The parent component
     *
     * @var Application
     */
    protected $parentComponent;

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService
     */
    private $publicationService;

    /**
     * PublicationHandler constructor.
     *
     * @param PublicationForm $publicationForm
     * @param Application $parentComponent
     */
    public function __construct(
        PublicationForm $publicationForm, Application $parentComponent, PublicationService $publicationService
    )
    {
        $this->publicationForm = $publicationForm;
        $this->parentComponent = $parentComponent;
        $this->publicationService = $publicationService;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService
     */
    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    /**
     * Publishes the actual selected and configured content objects
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $selectedContentObjects
     */
    public function publish($selectedContentObjects = [])
    {
        $translator = Translation::getInstance();
        $values = $this->publicationForm->exportValues();

        $selectedUserIdentifiers = (array) $values[PublicationForm::PARAM_SHARE][UserEntityProvider::ENTITY_TYPE];
        $selectedGroupIdentifiers = (array) $values[PublicationForm::PARAM_SHARE][GroupEntityProvider::ENTITY_TYPE];

        $publicationResult = true;

        foreach ($selectedContentObjects as $selectedContentObject)
        {
            $publicationResult = $this->getPublicationService()->createPublicationWithRightsFromParameters(
                $selectedContentObject->getId(), $this->publicationForm->getFormUser()->getId(),
                $selectedUserIdentifiers, $selectedGroupIdentifiers
            );

            if (!$publicationResult)
            {
                break;
            }
        }

        if (!$publicationResult)
        {
            $message = $translator->getTranslation(
                'ObjectNotPublished',
                array('OBJECT' => $translator->getTranslation('PersonalCalendar', null, Manager::CONTEXT)),
                StringUtilities::LIBRARIES
            );
        }
        else
        {
            $message = $translator->getTranslation(
                'ObjectPublished',
                array('OBJECT' => $translator->getTranslation('PersonalCalendar', null, Manager::CONTEXT)),
                StringUtilities::LIBRARIES
            );
        }

        $this->parentComponent->redirectWithMessage(
            $message, !$publicationResult, array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Application\Calendar\Manager::ACTION_BROWSE
            )
        );
    }
}