<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ShareComponent extends Manager
{

    public function run()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = 'Let\'s share !';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}