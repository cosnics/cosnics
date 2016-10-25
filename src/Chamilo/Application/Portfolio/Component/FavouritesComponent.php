<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * Shows the favourites for the current user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouritesComponent extends TabComponent implements DelegateComponent
{
    /**
     * Executes this component
     */
    public function build()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Application\Portfolio\Favourite\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        );

        return $factory->run();
    }
}