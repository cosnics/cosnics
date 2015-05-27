<?php
namespace Chamilo\Core\MetadataOld\Component;

use Chamilo\Core\MetadataOld\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ControlledVocabularyComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\MetadataOld\ControlledVocabulary\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
