<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Publisher;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
     * PublicationHandler constructor.
     * 
     * @param PublicationForm $publicationForm
     * @param Application $parentComponent
     */
    public function __construct(PublicationForm $publicationForm, Application $parentComponent)
    {
        $this->publicationForm = $publicationForm;
        $this->parentComponent = $parentComponent;
    }

    /**
     * Publishes the actual selected and configured content objects
     * 
     * @param ContentObject[] $selectedContentObjects
     */
    public function publish($selectedContentObjects = array())
    {
        $translator = Translation::getInstance();
        $publication = $this->publicationForm->create_content_object_publications();
        
        if (! $publication)
        {
            $message = $translator->getTranslation(
                'ObjectNotPublished', 
                array('OBJECT' => $translator->getTranslation('PersonalCalendar', null, Manager::context())), 
                Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $message = $translator->getTranslation(
                'ObjectPublished', 
                array('OBJECT' => $translator->getTranslation('PersonalCalendar', null, Manager::context())), 
                Utilities::COMMON_LIBRARIES);
        }
        
        $this->parentComponent->redirect(
            $message, 
            (! $publication ? true : false), 
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Calendar\Manager::ACTION_BROWSE));
    }
}