<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Component;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Publication\ContentObjectPublicationHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormInterface;

class PublisherComponent extends Manager implements PublisherCustomPublicationFormInterface,
    PublisherCustomPublicationFormHandler
{

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    /**
     * Constructs the publication form
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication[] $publications
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $selectedContentObjects
     *
     * @return ContentObjectPublicationForm
     */
    public function constructPublicationForm($publications, $selectedContentObjects)
    {
        $course = $this->get_course();
        $is_course_admin = $course->is_course_admin($this->getUser());

        return new PublicationForm(
            $this->getUser(),
            PublicationForm::TYPE_CREATE,
            $publications,
            $course,
            $this->get_url(),
            $is_course_admin,
            $selectedContentObjects, $this->getTranslator(), $this->getPublicationService()
        );
    }

    /**
     * Constructs the publication form
     *
     * @param ContentObjectPublicationForm $publicationForm
     *
     * @return PublicationHandlerInterface
     */
    public function getPublicationHandler(ContentObjectPublicationForm $publicationForm)
    {
        return new ContentObjectPublicationHandler(
            $this->get_course_id(),
            $this->get_tool_id(),
            $this->getUser(),
            $this,
            $publicationForm
        );
    }
}
