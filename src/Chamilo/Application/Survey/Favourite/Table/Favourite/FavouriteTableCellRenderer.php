<?php
namespace Chamilo\Application\Survey\Favourite\Table\Favourite;

use Chamilo\Application\Survey\Favourite\Manager;
use Chamilo\Application\Survey\Table\Publication\PublicationTableCellRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteTableCellRenderer extends PublicationTableCellRenderer
{

    /**
     *
     * @see \Chamilo\Application\Survey\Table\Publication\PublicationTableCellRenderer::get_actions()
     */
    public function get_actions($publication)
    {
        $toolbar = $this->getToolbar($publication);

        $unfavouriteItem = new ToolbarItem(
            Translation :: get('Unfavourite', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Delete'),
            $this->get_component()->get_url(
                array(
                    Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                    \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID => $publication->get_id())),
            ToolbarItem :: DISPLAY_ICON,
            true);

        $toolbar->replace_item($unfavouriteItem, 0);

        return $toolbar->as_html();
    }
}
