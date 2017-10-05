<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.user.component
 */
class EmailerComponent extends Manager
{

    public function run()
    {
        $ids = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            foreach ($ids as $id)
            {
                $users[] = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    $id);
            }

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\User\Email\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component->set_target_users($users);
            $component->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $ids);
            return $component->run();
        }
        else
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException(Translation::get('User'));
        }
    }

    public function render_header($trail)
    {
        $ids = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, null);

        $trail = BreadcrumbTrail::getInstance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(array(\Chamilo\Application\Weblcms\Manager::PARAM_USERS => $ids)),
                Translation::get('EmailUsers')));

        return parent::render_header();
    }
}
