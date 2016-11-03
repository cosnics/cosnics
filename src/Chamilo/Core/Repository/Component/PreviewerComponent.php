<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PreviewerComponent extends Manager
{

    public function run()
    {
        $content_object = $this->get_root_content_object();

        if (! RightsService :: getInstance()->canViewContentObject(
            $this->get_user(),
            $content_object,
            $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(self :: PARAM_CONTENT_OBJECT_ID, $content_object->get_id());

        if ($content_object)
        {
            Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);

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
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $content_object_id);
    }

    public function getPreview()
    {
        $contentObjectClassname = $this->get_root_content_object()->get_type();
        $contentObjectNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
            $contentObjectClassname);
        $contentObjectNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($contentObjectNamespace, 2);
        $namespace = $contentObjectNamespace . '\Display\Preview';
        $factory = new ApplicationFactory(
            $namespace,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory;
    }
}
