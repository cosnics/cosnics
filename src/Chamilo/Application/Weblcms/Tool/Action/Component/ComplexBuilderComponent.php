<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: complex_builder.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.component
 */
class ComplexBuilderComponent extends Manager
{

    private $content_object;

    public function run()
    {
        $pid = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $pid);

        $content_object = $publication->get_content_object();

        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation :: get('ToolComplexBuilderComponent', array('TITLE' => $content_object->get_title()))));

        if (! ($content_object->get_owner_id() == $this->get_user_id()) && ! RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
            RepositoryRights :: COLLABORATE_RIGHT,
            $content_object->get_id(),
            RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
            $content_object->get_owner_id()))
        {
            $this->redirect(
                Translation :: get("NotAllowed"),
                '',
                array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
        else
        {
            $this->content_object = $publication->get_content_object();
            $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, $pid);

            $context = $this->content_object->package() . '\Builder';

            $application_factory = new ApplicationFactory($context, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $application_factory->run();
        }
    }

    public function get_root_content_object()
    {
        return $this->content_object;
    }
}
