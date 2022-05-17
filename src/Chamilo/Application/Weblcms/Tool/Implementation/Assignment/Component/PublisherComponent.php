<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Publication\ContentObjectPublicationHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormInterface;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package application.weblcms.tool.assignment.php.component
 *          Publisher for assignment publications.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class PublisherComponent extends Manager
    implements PublisherCustomPublicationFormInterface, PublisherCustomPublicationFormHandler
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
            $this->getUser(), PublicationForm::TYPE_CREATE, $publications, $course, $this->get_url(), $is_course_admin,
            $selectedContentObjects, $this->getTranslator(), $this->getPublicationRepository()
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
