<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Glossary\Display\Preview\Manager implements GlossaryDisplaySupport
{


    public function run()
    {

        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
