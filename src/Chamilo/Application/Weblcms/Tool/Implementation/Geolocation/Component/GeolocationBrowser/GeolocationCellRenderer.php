<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component\GeolocationBrowser;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\TableColumn;

/**
 *
 * @package application.lib.weblcms.tool.geolocation.component.geolocation.browser
 */
/**
 * This class is a cell renderer for a publication candidate table
 */
class GeolocationCellRenderer extends ObjectPublicationTableCellRenderer
{

    public function __construct($browser)
    {
        parent::__construct($browser);
    }

    /*
     * Inherited
     */
    public function renderCell(TableColumn $column, $publication): string
    {
        if ($column === ObjectPublicationTableColumnModel::get_action_column())
        {
            return $this->get_actions($publication, null, true, false)->as_html();
        }

        return parent::renderCell($column, $publication);
    }
}
