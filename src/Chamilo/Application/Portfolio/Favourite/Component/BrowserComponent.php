<?php
namespace Chamilo\Application\Portfolio\Favourite\Component;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Application\Portfolio\Favourite\Table\FavouriteTableRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * Browser for the favourites of the current user
 *
 * @package Chamilo\Application\Portfolio\Favourite\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getFavouriteTableRenderer(): FavouriteTableRenderer
    {
        return $this->getService(FavouriteTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getFavouriteService()->countFavouriteUsers($this->getUser());
        $favouriteTableRenderer = $this->getFavouriteTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $favouriteTableRenderer->getParameterNames(), $favouriteTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getFavouriteService()->findFavouriteUsers(
            $this->getUser(), null, $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $favouriteTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $favouriteTableRenderer->render($tableParameterValues, $users);
    }
}