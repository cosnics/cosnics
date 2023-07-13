<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.component
 */
class ComplexBuilderComponent extends Manager implements DelegateComponent
{

    private $content_object;

    public function run()
    {
        $pid = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pid
        );

        $content_object = $publication->get_content_object();

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation::get('ToolComplexBuilderComponent', ['TITLE' => $content_object->get_title()])
            )
        );

        $this->content_object = $publication->get_content_object();
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $pid);

        $context = $this->content_object::CONTEXT . '\Builder';

        return $this->getApplicationFactory()->getApplication(
            $context, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function get_root_content_object()
    {
        return $this->content_object;
    }
}
