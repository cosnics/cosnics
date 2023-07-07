<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Publication\ContentObjectPublicationHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormInterface;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Assessment Publisher Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublisherComponent extends Manager
    implements PublisherCustomPublicationFormInterface, PublisherCustomPublicationFormHandler
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    /**
     * Constructs the publication form
     *
     * @param ContentObjectPublication[] $publications
     * @param ContentObject[] $selectedContentObjects
     *
     * @return ContentObjectPublicationForm
     */
    public function constructPublicationForm($publications, $selectedContentObjects)
    {
        $course = $this->get_course();
        $is_course_admin = $course->is_course_admin($this->getUser());

        return new PublicationForm(
            $this->getUser(), PublicationForm::TYPE_CREATE, $publications, $course, $this->get_url(), $is_course_admin,
            $selectedContentObjects
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
            $this->get_course_id(), $this->get_tool_id(), $this->getUser(), $this, $publicationForm
        );
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
