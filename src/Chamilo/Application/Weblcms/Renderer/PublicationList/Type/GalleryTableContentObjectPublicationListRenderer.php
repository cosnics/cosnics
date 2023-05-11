<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Table\ObjectPublicationGalleryTableRenderer;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Renderer to display a sortable table with learning object publications.
 *
 * @author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GalleryTableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    public function __construct($tool_browser, $parameters = [])
    {
        parent::__construct($tool_browser, $parameters);
        $this->addWarning();
    }

    public function addWarning()
    {
        $this->getNotificationMessageManager()->addMessage(
            new NotificationMessage(Translation::get('BrowserWarningPreview'), NotificationMessage::TYPE_WARNING)
        );
    }

    /**
     * Returns the HTML output of this renderer.
     *
     * @return string The HTML output
     */
    public function as_html()
    {
        return $this->renderTable();
    }

    protected function getNotificationMessageManager(): NotificationMessageManager
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            NotificationMessageManager::class
        );
    }

    public function getObjectPublicationGalleryTableRenderer(): ObjectPublicationGalleryTableRenderer
    {
        return $this->getService(ObjectPublicationGalleryTableRenderer::class);
    }

    /**
     * Returns the parameters that the table needs for the url building
     *
     * @return string[]
     */
    public function get_parameters()
    {
        return $this->get_tool_browser()->get_parameters();
    }

    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->countContentObjectPublications();
        $objectPublicationGalleryTableRenderer = $this->getObjectPublicationGalleryTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $objectPublicationGalleryTableRenderer->getParameterNames(),
            $objectPublicationGalleryTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $contentObjectPublications = $this->retrieveContentObjectPublications(
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $objectPublicationGalleryTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $objectPublicationGalleryTableRenderer->legacyRender(
            $this, $tableParameterValues, $contentObjectPublications
        );
    }
}
