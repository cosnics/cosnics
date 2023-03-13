<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class PreviewerComponent extends Manager
{

    public function run()
    {
        $content_object = $this->get_root_content_object();

        if (!$this->getWorkspaceRightsService()->canViewContentObject(
            $this->get_user(), $content_object, $this->getWorkspace()
        ))
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $content_object->get_id());

        if ($content_object)
        {
            $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

            return $this->getPreview()->run();
        }
        else
        {
            return $this->display_error_page(Translation::get('NoObjectSelected', null, StringUtilities::LIBRARIES));
        }
    }

    public function getPreview()
    {
        $contentObjectClassname = $this->get_root_content_object()->getType();
        $contentObjectNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($contentObjectClassname);
        $contentObjectNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($contentObjectNamespace, 2);
        $namespace = $contentObjectNamespace . '\Display\Preview';

        return $this->getApplicationFactory()->getApplication(
            $namespace, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );
    }

    public function get_root_content_object()
    {
        $content_object_id = Request::get(self::PARAM_CONTENT_OBJECT_ID);

        return DataManager::retrieve_by_id(
            ContentObject::class, $content_object_id
        );
    }
}
