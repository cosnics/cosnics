<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.user.component
 */
class EmailerComponent extends Manager
{

    public function run()
    {
        $ids = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if (!is_array($ids))
        {
            $ids = [$ids];
        }

        if (count($ids) > 0)
        {
            foreach ($ids as $id)
            {
                $users[] = DataManager::retrieve_by_id(
                    User::class, $id
                );
            }

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\User\Email\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_target_users($users);
            $component->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $ids);

            return $component->run();
        }
        else
        {
            throw new NoObjectSelectedException(Translation::get('User'));
        }
    }

    public function render_header(string $pageTitle = ''): string
    {
        $ids = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, null);

        $trail = $this->getBreadcrumbTrail();

        $trail->add(
            new Breadcrumb(
                $this->get_url([\Chamilo\Application\Weblcms\Manager::PARAM_USERS => $ids]),
                Translation::get('EmailUsers')
            )
        );

        return parent::render_header();
    }
}
