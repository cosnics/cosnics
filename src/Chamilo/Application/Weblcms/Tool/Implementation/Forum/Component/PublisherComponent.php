<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Publication\ContentObjectPublicationHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormHandler;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;

class PublisherComponent extends Manager implements PublisherCustomPublicationFormHandler
{

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

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID;

        return parent::get_additional_parameters($additionalParameters);
    }
}
