<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Component;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite\FavouriteTable;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    public function run()
    {
        $table = new FavouriteTable($this);
        
        $html = array();
        
        $html[] = $this->render_header();
        
        if (! $this->hasFavourites())
        {
            $html[] = '<div class="alert alert-info">';
            $html[] = Translation::getInstance()->getTranslation('FavouritesInfo', null, Manager::context());
            $html[] = '</div>';
        }
        
        $html[] = $table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class_name(), 
                WorkspaceUserFavourite::PROPERTY_USER_ID), 
            new StaticConditionVariable($this->get_user_id()));
    }

    /**
     * Checks if the current user has favourites
     * 
     * @return bool
     */
    protected function hasFavourites()
    {
        return ($this->getWorkspaceService()->countWorkspaceFavouritesByUser(
            $this->getEntityService(), 
            $this->getUser()) > 0);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\EntityService
     */
    protected function getEntityService()
    {
        if (! isset($this->entityService))
        {
            $this->entityService = new EntityService();
        }
        
        return $this->entityService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    protected function getWorkspaceService()
    {
        if (! isset($this->workspaceService))
        {
            $this->workspaceService = new WorkspaceService(new WorkspaceRepository());
        }
        
        return $this->workspaceService;
    }
}