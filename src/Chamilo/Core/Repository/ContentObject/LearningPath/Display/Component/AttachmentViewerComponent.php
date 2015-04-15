<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class AttachmentViewerComponent extends TabComponent
{

    public function build()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Display\Action\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
