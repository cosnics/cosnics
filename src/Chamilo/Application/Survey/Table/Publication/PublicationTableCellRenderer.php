<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository;
use Chamilo\Application\Survey\Favourite\Service\FavouriteService;
use Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer::render_cell()
     */
    public function render_cell($column, $publication)
    {
        switch ($column->get_name())
        {
            case Publication :: PROPERTY_PUBLISHER_ID :
                return $publication->getPublisher()->get_fullname();
            case Publication :: PROPERTY_PUBLISHED :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES), 
                    $publication->getPublished());
            case Publication :: PROPERTY_TITLE :
                if (RightsService :: getInstance()->canTakeSurvey($this->get_component()->get_user(), $publication))
                {
                    return '<a href="' . $this->getPublicationUrl($publication) . '">' .
                         parent :: render_cell($column, $publication) . '</a>';
                }
        }
        
        return parent :: render_cell($column, $publication);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($publication)
    {
        return $this->getToolbar($publication)->as_html();
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @return \Chamilo\Libraries\Format\Structure\Toolbar
     */
    public function getToolbar($publication)
    {
        $toolbar = new Toolbar();
        
        $favouriteService = new FavouriteService(new FavouriteRepository());
        $favourite = $favouriteService->getPublicationUserFavouriteByUserAndPublicationIdentifier(
            $this->get_component()->get_user(), 
            $publication->getId());
        
        if ($favourite instanceof PublicationUserFavourite)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('FavouriteNa', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/FavouriteNa'), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Favourite', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Favourite'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_FAVOURITE, 
                            \Chamilo\Application\Survey\Favourite\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Favourite\Manager :: ACTION_CREATE, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if (RightsService :: getInstance()->canTakeSurvey($this->get_component()->get_user(), $publication))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Take', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Take'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_TAKE, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if (RightsService :: getInstance()->canMail($this->get_component()->get_user(), $publication))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Mail', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Mail'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_MAIL, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if (RightsService :: getInstance()->canViewAndExportResults($this->get_component()->get_user(), $publication))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Report', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Reporting'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_REPORT, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if (RightsService :: getInstance()->canManagePublication($this->get_component()->get_user(), $publication))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Rights', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_RIGHTS, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE, 
                            Manager :: PARAM_PUBLICATION_ID => $publication->get_id())), 
                    ToolbarItem :: DISPLAY_ICON, 
                    true));
        }
        
        return $toolbar;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @return string
     */
    private function getPublicationUrl($publication)
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Application\Survey\Manager :: package(), 
                \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_VIEW, 
                \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID => $publication->getId()));
        
        return $redirect->getUrl();
    }
}
