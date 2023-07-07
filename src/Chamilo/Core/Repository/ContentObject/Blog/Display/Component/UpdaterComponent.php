<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component;

use Chamilo\Core\Repository\ContentObject\Blog\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package repository.lib.complex_builder.blog.component
 */
class UpdaterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Display\Action\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT)),
                $this->get_root_content_object()->get_title()));
    }
}
