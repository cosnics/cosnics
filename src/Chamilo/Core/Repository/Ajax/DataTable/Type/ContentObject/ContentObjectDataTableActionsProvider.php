<?php

namespace Chamilo\Core\Repository\Ajax\DataTable\Type\ContentObject;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\DataAction\DataActions;
use Chamilo\Libraries\Format\DataAction\RedirectAction;
use Chamilo\Libraries\Format\DataTable\DataTableActionsProvider;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\DataTable\Type\ContentObject
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class ContentObjectDataTableActionsProvider extends DataTableActionsProvider
{
    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $dataClass
     *
     * @return \Chamilo\Libraries\Format\DataAction\DataActions
     */
    public function getDataClassActions(ContentObject $dataClass)
    {
        $actions = new DataActions();

        $actions->addAction(new RedirectAction('test', 'test', 'index.php', 'fa-plus'));

        return $actions;
    }
}