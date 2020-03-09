<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

set_time_limit(0);

class PackageDeactivator extends Action
{

    public function run()
    {
        $deactivator = Deactivator::factory($this->get_package()->get_context());
        if (!$deactivator->run())
        {
            $this->add_message($deactivator->retrieve_message());
            $title = Translation::get(
                'Failed', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('sad-cry', array('fa-lg'), null, 'fas');

            return $this->action_failed($title, $image);
        }
        else
        {
            $this->add_message($deactivator->retrieve_message());
            $title = Translation::get(
                'Finished', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('laugh-beam', array('fa-lg'), null, 'fas');

            return $this->action_successful($title, $image);
        }
    }
}
