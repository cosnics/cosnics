<?php

namespace Chamilo\Application\ExamAssignment\Component;

use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * Class EntryComponent
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class EntryComponent extends Manager
{

    function run()
    {
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(
            Manager::context() . ':Entry.html.twig',
            ['HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer()]
        );
    }
}
