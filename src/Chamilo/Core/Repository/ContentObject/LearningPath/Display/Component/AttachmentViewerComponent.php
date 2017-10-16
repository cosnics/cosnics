<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class AttachmentViewerComponent extends BaseHtmlTreeComponent
{

    public function build()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Display\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
