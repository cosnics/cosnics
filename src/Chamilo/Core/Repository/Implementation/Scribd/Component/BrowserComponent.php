<?php
namespace Chamilo\Core\Repository\Implementation\Scribd\Component;

use Chamilo\Core\Repository\Implementation\Scribd\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! ActionBarSearchForm :: get_query())
        {
            Request :: set_get(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, 'chamilo');
        }

        return parent :: run();
    }
}
