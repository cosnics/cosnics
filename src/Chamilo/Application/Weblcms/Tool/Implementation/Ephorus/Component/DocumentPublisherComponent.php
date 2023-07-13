<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Viewer\Architecture\Traits\ViewerTrait;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class sends a request for a document to ephorus
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DocumentPublisherComponent extends Manager implements ViewerInterface
{
    use ViewerTrait;

    /**
     * Runs this component
     */
    public function run()
    {
        $this->validateAccess();

        if (!$this->isAnyObjectSelectedInViewer())
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);

            return $component->run();
        }
        else
        {
            $objects = $this->getObjectsSelectedInviewer();

            $parameters = [
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CREATE,
                Manager::PARAM_CONTENT_OBJECT_IDS => $objects
            ];

            $this->redirectWithMessage('', false, $parameters);
        }
    }

    /**
     * Returns the allowed content object types for the repoviewer
     *
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        return [File::class];
    }

    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);
        $html[] = $this->display_warning_message(Translation::get('EphorusMaxUploadSize'));

        return implode(PHP_EOL, $html);
    }
}
