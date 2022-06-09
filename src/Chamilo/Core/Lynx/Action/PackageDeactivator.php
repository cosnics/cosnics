<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

class PackageDeactivator extends AbstractAction
{

    public function run(): bool
    {
        set_time_limit(0);

        $deactivator = Deactivator::factory($this->getPackage()->get_context());

        if (!$deactivator->run())
        {
            $this->add_message($deactivator->retrieve_message());
            $title = Translation::get(
                'Failed', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('sad-cry', ['fa-lg'], null, 'fas');

            return $this->hasFailed($title, $image);
        }
        else
        {
            $this->add_message($deactivator->retrieve_message());
            $title = Translation::get(
                'Finished', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('laugh-beam', ['fa-lg'], null, 'fas');

            return $this->wasSuccessful($title, $image);
        }
    }
}
