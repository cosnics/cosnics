<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Publication\Table\PublicationTableRenderer;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * Repository manager component which displays user's publications.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class BrowserComponent extends Manager implements BreadcrumbLessComponentInterface
{
    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getPublicationAggregator(): PublicationAggregator
    {
        return $this->getService(PublicationAggregator::class);
    }

    public function getPublicationTableRenderer(): PublicationTableRenderer
    {
        return $this->getService(PublicationTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getPublicationAggregator()->countPublicationAttributes(
            PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER, (int) $this->getUser()->getId()
        );

        $publicationTableRenderer = $this->getPublicationTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $publicationTableRenderer->getParameterNames(), $publicationTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $attributes = $this->getPublicationAggregator()->getContentObjectPublicationsAttributes(
            PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER, (int) $this->getUser()->getId(), null,
            $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $publicationTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $publicationTableRenderer->render($tableParameterValues, $attributes);
    }

}
