<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Integration\Chamilo\Core\Reporting\Template\ProgressDetailsTemplate;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Integration\Chamilo\Core\Reporting\Template\ProgressTemplate;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class ReportingComponent extends TabComponent
{

    public function build()
    {
        if ($this->is_current_step_set())
        {
            $template_type = ProgressDetailsTemplate :: class_name();
        }
        else
        {
            $template_type = ProgressTemplate :: class_name();
        }

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Reporting\Viewer\Manager :: context(),
            $this->get_user(),
            $this);
        $component = $factory->getComponent();
        $component->set_template_by_name($template_type);
        return $component->run();
    }
   
}
