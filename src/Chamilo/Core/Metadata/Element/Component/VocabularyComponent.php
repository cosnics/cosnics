<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class VocabularyComponent extends Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(), 
            \Chamilo\Core\Metadata\Vocabulary\Manager :: context(), 
            $this->get_user(), 
            $this);
        return $factory->run();
    }
}