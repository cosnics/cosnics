<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Header;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ExternalInstanceComponent extends Manager implements ApplicationSupport
{

    private $external_instance;

    public function run()
    {
        Header :: get_instance()->set_section('external_repository');

        $trail = BreadcrumbTrail :: get_instance();
        $trail->remove($trail->size() - 1);
        $trail->add(
            new Breadcrumb(
                $this->get_url(),
                Translation :: get(
                    'ExternalInstance',
                    null,
                    \Chamilo\Core\Repository\Instance\Manager :: get_namespace())));

        $external_instance_id = Request :: get(self :: PARAM_EXTERNAL_INSTANCE);
        $this->set_parameter(self :: PARAM_EXTERNAL_INSTANCE, $external_instance_id);
        $this->external_instance = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_by_id(
            Instance :: class_name(),
            $external_instance_id);

        if ($this->external_instance instanceof Instance && $this->external_instance->is_enabled())
        {
            $manager = ClassnameUtilities :: getInstance()->getNamespaceParent(
                ClassnameUtilities :: getInstance()->getNamespaceParent($this->get_external_instance()->get_type())) .
                 '\Manager';
            $manager :: launch($this);
        }
        else
        {
            return $this->display_error_page('NoSuchExternalInstanceManager');
        }
    }

    public function get_external_instance()
    {
        return $this->external_instance;
    }
}
