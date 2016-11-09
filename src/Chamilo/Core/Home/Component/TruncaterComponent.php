<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package Chamilo\Core\Home\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TruncaterComponent extends Manager
{

    public function run()
    {
        $success = DataManager :: truncateHome($this->get_user_id());

        $redirect = new Redirect();
        $redirect->toUrl();
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: getInstance());
    }
}
