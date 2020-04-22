<?php
namespace Chamilo\Application\Weblcms\Publication;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;
use RuntimeException;

/**
 * Content Object Publication Handler for the Weblcms Application
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationHandler implements PublicationHandlerInterface
{

    /**
     * The content object publication form
     * 
     * @var ContentObjectPublicationForm
     */
    protected $contentObjectPublicationForm;

    /**
     * The course in which the publications must be made
     * 
     * @var int
     */
    protected $courseId;

    /**
     * The tool in which the publications must be made
     * 
     * @var string
     */
    protected $toolId;

    /**
     * The user that makes the publications
     * 
     * @var User
     */
    protected $user;

    /**
     * The parent component in which this handler runs
     * 
     * @var Application
     */
    protected $parentComponent;

    /**
     * The created publications
     * 
     * @var ContentObjectPublication[]
     */
    protected $createdPublications;

    /**
     * ContentObjectPublicationHandler constructor.
     * 
     * @param int $courseId
     * @param int $toolId
     * @param User $user
     * @param Application $parentComponent
     * @param ContentObjectPublicationForm $contentObjectPublicationForm
     */
    public function __construct($courseId, $toolId, User $user, Application $parentComponent, 
        ContentObjectPublicationForm $contentObjectPublicationForm = null)
    {
        $this->courseId = $courseId;
        $this->toolId = $toolId;
        $this->user = $user;
        $this->parentComponent = $parentComponent;
        $this->contentObjectPublicationForm = $contentObjectPublicationForm;
    }

    /**
     * Publishes the actual selected and configured content objects
     * 
     * @param ContentObject[] $selectedContentObjects
     *
     * @return bool
     */
    public function publish($selectedContentObjects = array())
    {
        if (! $this->hasForm())
        {
            
            $success = $this->createPublicationsWithoutForm($selectedContentObjects);
        }
        else
        {
            $success = $this->contentObjectPublicationForm->handle_form_submit();
            $this->createdPublications = $this->contentObjectPublicationForm->get_publications();
        }
        
        $message = Translation::getInstance()->getTranslation(
            ($success ? 'ObjectPublished' : 'ObjectNotPublished'), 
            array('OBJECT' => Translation::get('Object')), 
            Utilities::COMMON_LIBRARIES);
        
        $parameters = array(
            Manager::PARAM_ACTION => Manager::ACTION_BROWSE);
        
        if ($this->is_publish_and_build_submit())
        {
            $parameters = $this->getBuilderParameters();
        }
        
        if ($this->is_publish_and_view_submit())
        {
            $parameters = $this->getDisplayParameters();
        }
        
        $this->parentComponent->redirect($message, ! $success, $parameters);
    }

    /**
     * Creates the publications without the publications form
     * 
     * @param ContentObject[] $selectedContentObjects
     *
     * @return bool
     */
    public function createPublicationsWithoutForm($selectedContentObjects = array())
    {
        $success = true;
        
        foreach ($selectedContentObjects as $contentObject)
        {
            try
            {
                $publication = $this->createPublicationForContentObject($contentObject);
                $this->createdPublications[] = $publication;
            }
            catch (Exception $ex)
            {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Creates a content object publication for a given content object
     * 
     * @param ContentObject $contentObject
     *
     * @return ContentObjectPublication
     */
    protected function createPublicationForContentObject($contentObject)
    {
        $publication = new ContentObjectPublication();
        
        $publication->set_content_object_id($contentObject->getId());
        $publication->set_course_id($this->courseId);
        $publication->set_tool($this->toolId);
        $publication->set_publisher_id($this->user->getId());
        $publication->set_publication_publisher($this->user);
        $publication->set_category_id(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);
        $publication->set_publication_date(time());
        $publication->set_modified_date(time());
        $publication->set_hidden(0);
        $publication->set_show_on_homepage(0);
        
        if (! $publication->create())
        {
            throw new RuntimeException(
                'Could not create the publication for content object with id ' . $contentObject->getId());
        }
        
        return $publication;
    }

    /**
     * Returns the if the submit action is publish and build
     */
    protected function is_publish_and_build_submit()
    {
        if (! $this->hasForm())
        {
            return false;
        }
        
        $values = $this->contentObjectPublicationForm->exportValues();
        
        return ! empty($values[ContentObjectPublicationForm::PROPERTY_PUBLISH_AND_BUILD]);
    }

    /**
     * Returns the if the submit action is publish and view
     */
    protected function is_publish_and_view_submit()
    {
        if (! $this->hasForm())
        {
            return false;
        }
        $values = $this->contentObjectPublicationForm->exportValues();
        
        return ! empty($values[ContentObjectPublicationForm::PROPERTY_PUBLISH_AND_VIEW]);
    }

    /**
     * Returns whether or not the publisher uses a form
     * 
     * @return bool
     */
    protected function hasForm()
    {
        return $this->contentObjectPublicationForm instanceof ContentObjectPublicationForm;
    }

    /**
     * Returns the necessary parameters to redirect to the builder
     * 
     * @return string[]
     */
    protected function getBuilderParameters()
    {
        $parameters = array();

        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BUILD_COMPLEX_CONTENT_OBJECT;

        $parameters[Manager::PARAM_PUBLICATION_ID] = $this->createdPublications[0]->getId();
        
        return $parameters;
    }

    /**
     * Returns the necessary parameters to redirect to the complex display
     * 
     * @return mixed
     */
    protected function getDisplayParameters()
    {
        $parameters = array();

        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;

        $parameters[Manager::PARAM_PUBLICATION_ID] = $this->createdPublications[0]->getId();
        
        return $parameters;
    }
}