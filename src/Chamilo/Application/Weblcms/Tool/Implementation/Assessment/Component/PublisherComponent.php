<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Assessment Publisher Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublisherComponent extends Manager implements PublisherCustomPublicationFormInterface
{

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
            $this->getUser(),
            PublicationForm :: TYPE_CREATE,
            $publications,
            $course,
            $this->get_url(),
            $is_course_admin,
            $selectedContentObjects);
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
