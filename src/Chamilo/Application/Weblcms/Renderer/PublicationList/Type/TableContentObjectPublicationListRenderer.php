<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Table\ObjectPublicationTableRenderer;

/**
 * Renderer to display a sortable table with learning object publications.
 *
 * @package application.weblcms
 * @author  Hans De Bisschop - EHB
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class TableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function as_html()
    {
        return $this->renderTable();
    }

    public function getObjectPublicationTableRenderer(): ObjectPublicationTableRenderer
    {
        $application = $this->get_tool_browser()->get_parent();
        $context = $application::CONTEXT;
        $publicationTableRendererName = $context . '\Table\ObjectPublicationTableRenderer';

        if (class_exists($publicationTableRendererName))
        {
            return $this->getService($publicationTableRendererName);
        }
        else
        {
            return $this->getService(ObjectPublicationTableRenderer::class);
        }
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

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->countContentObjectPublications();
        $objectPublicationTableRenderer = $this->getObjectPublicationTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $objectPublicationTableRenderer->getParameterNames(),
            $objectPublicationTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $contentObjectPublications = $this->retrieveContentObjectPublications(
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $objectPublicationTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $objectPublicationTableRenderer->legacyRender($this, $tableParameterValues, $contentObjectPublications);
    }

}
