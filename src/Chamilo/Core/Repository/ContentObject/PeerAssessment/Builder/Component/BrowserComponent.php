<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Platform\Translation;

class BrowserComponent extends Manager
{

    public function run()
    {
        // is context repository?
        if ($this->get_parent() instanceof \Chamilo\Core\Repository\Component\BuilderComponent &&
             $this->get_root_content_object()->get_assessment_type() != PeerAssessment :: TYPE_FEEDBACK)
        {
            $factory = new ApplicationFactory(
                $this->getRequest(),
                \Chamilo\Core\Repository\Builder\Action\Manager :: context(),
                $this->get_user(),
                $this);
            return $factory->run();
        }
        else
        {
            $publication_has_scores = $this->publication_has_scores();
            // is context tool or app?

            if ($this->get_root_content_object()->get_assessment_type() != PeerAssessment :: TYPE_FEEDBACK &&
                 ! $publication_has_scores)
            {
                $factory = new ApplicationFactory(
                    $this->getRequest(),
                    \Chamilo\Core\Repository\Builder\Action\Manager :: context(),
                    $this->get_user(),
                    $this);
                return $factory->run();
            }
            else
            {
                $this->redirect(null, false, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ATTEMPTS));
            }
        }
    }

    function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = '<div class="context_info notification-2">' . Translation :: get('IndicatorInfoMessage') . '</div>';

        return implode(PHP_EOL, $html);
    }
}
