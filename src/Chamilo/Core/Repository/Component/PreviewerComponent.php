<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class PreviewerComponent extends Manager
{

    public function render_header()
    {
        return parent :: render_small_header();
    }

    public function render_footer()
    {
        return $this->render_small_footer();
    }

    public function run()
    {
        $content_object = $this->get_root_content_object();
        $this->set_parameter(self :: PARAM_CONTENT_OBJECT_ID, $content_object->get_id());

        if ($content_object)
        {
            $preview_factory = $this->getPreview();
            return $preview_factory->run();
        }
        else
        {
            return $this->display_error_page(
                Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
        }
    }

    public function get_root_content_object()
    {
        $content_object_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($content_object_id);
    }

    public function getPreview()
    {
        $contentObjectClassname = $this->get_root_content_object()->get_type();
        $contentObjectNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
            $contentObjectClassname);
        $contentObjectNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($contentObjectNamespace, 2);
        $namespace = $contentObjectNamespace . '\Display\Preview';
        $factory = new ApplicationFactory($this->getRequest(), $namespace, $this->get_user(), $this);
        return $factory;
    }
}
