<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Package\Action\Activator;
use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

set_time_limit(0);

class PackageActivator extends Action
{

    public function run()
    {
        $activator = Activator::factory($this->get_package()->get_context());
        if (!$activator->run())
        {
            $this->add_message($activator->retrieve_message());
            $title = Translation::get(
                'Failed', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('sad-cry', array('fa-lg'), null, 'fas');

            return $this->action_failed($title, $image);
        }
        else
        {
            $this->add_message($activator->retrieve_message());
            $title = Translation::get(
                'Finished', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('laugh-beam', array('fa-lg'), null, 'fas');

            return $this->action_successful($title, $image);
        }
    }
}
