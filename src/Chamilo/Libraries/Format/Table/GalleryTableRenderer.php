<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class GalleryTableRenderer extends AbstractTableRenderer
{
    use ClassContext;

    public const DEFAULT_NUMBER_OF_COLUMNS_PER_PAGE = 4;
    public const DEFAULT_NUMBER_OF_ROWS_PER_PAGE = 5;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, GalleryHtmlTableRenderer $htmlTableRenderer
    )
    {
        parent::__construct($translator, $urlGenerator, $htmlTableRenderer);
    }

    protected function processData(ArrayCollection $results, TableParameterValues $parameterValues): ArrayCollection
    {
        $tableData = [];
        $tableRow = [];

        foreach ($results as $result)
        {
            $tableRow[] = $this->renderCell($result, $parameterValues, $this->getTableActions());

            if (count($tableRow) >= $parameterValues->getNumberOfColumnsPerPage())
            {
                $tableData[] = $tableRow;
                $tableRow = [];
            }
        }

        return new ArrayCollection($tableData);
    }

    public function renderCell(
        $result, TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default panel-gallery">';

        $html[] = '<div class="panel-heading">';

        if ($tableActions instanceof TableActions && $tableActions->hasActions())
        {
            $identifierCellContent = $this->renderIdentifierCell($result);

            if (strlen($identifierCellContent) > 0)
            {
                $identifierCellContent =
                    $this->getCheckboxHtml($tableActions, $parameterValues, $identifierCellContent);
            }

            $html[] = $identifierCellContent;
        }

        $title = $this->renderTitle($result);

        $html[] = '<h3 class="panel-title" title="' . $title . '">';
        $html[] = $title;
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body panel-body-thumbnail text-center">';

        $html[] = $this->renderContent($result);
        $html[] = '</div>';

        if ($this instanceof TableRowActionsSupport)
        {
            $html[] = '<div class="panel-footer">';
            $html[] = $this->renderTableRowActions($result);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     */
    abstract public function renderContent($result): string;

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     */
    abstract public function renderTitle($result): string;
}
