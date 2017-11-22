<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class VocabularyComponent extends Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Metadata\Vocabulary\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}