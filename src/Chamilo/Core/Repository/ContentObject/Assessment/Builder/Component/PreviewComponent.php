<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * This component previews the complex content object
 *
 * @author Sven Vanpoucke
 */
class PreviewComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Builder\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
