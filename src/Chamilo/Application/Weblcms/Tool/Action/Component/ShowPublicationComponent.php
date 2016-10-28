<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ShowPublicationComponent extends ToggleVisibilityComponent implements DelegateComponent
{

    public function get_hidden()
    {
        return 0;
    }
}
