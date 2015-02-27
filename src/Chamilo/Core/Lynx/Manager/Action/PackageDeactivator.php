<?php
namespace Chamilo\Core\Lynx\Manager\Action;

use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

set_time_limit(0);
class PackageDeactivator extends Action
{

    public function run()
    {
        $deactivator = Deactivator :: factory($this->get_package()->get_context());
        if (! $deactivator->run())
        {
            $this->add_message($deactivator->retrieve_message());
            $title = Translation :: get(
                'Failed', 
                null, 
                ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__));
            $image = Theme :: getInstance()->getImagesPath(
                ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__)) . 'package_action/failed.png';
            return $this->action_failed($title, $image);
        }
        else
        {
            $this->add_message($deactivator->retrieve_message());
            $title = Translation :: get(
                'Finished', 
                null, 
                ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__));
            $image = Theme :: getInstance()->getImagesPath(
                ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__)) . 'package_action/finished.png';
            return $this->action_successful($title, $image);
        }
    }
}
