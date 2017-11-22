<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component\GeolocationBrowser;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;

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
    public function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel::get_action_column())
        {
            return $this->get_actions($publication, null, true, false)->as_html();
        }

        return parent::render_cell($column, $publication);
    }
}
