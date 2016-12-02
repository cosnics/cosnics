<?php
namespace Chamilo\Core\Lynx\Manager\Action;

use Chamilo\Configuration\Package\Action\Activator;
use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

set_time_limit(0);
class PackageActivator extends Action
{

    public function run()
    {
        $activator = Activator::factory($this->get_package()->get_context());
        if (! $activator->run())
        {
            $this->add_message($activator->retrieve_message());
            $title = Translation::get(
                'Failed', 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
            $image = Theme::getInstance()->getImagePath(
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
                'PackageAction/Failed');
            return $this->action_failed($title, $image);
        }
        else
        {
            $this->add_message($activator->retrieve_message());
            $title = Translation::get(
                'Finished', 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
            $image = Theme::getInstance()->getImagePath(
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
                'PackageAction/Finished');
            return $this->action_successful($title, $image);
        }
    }
}
