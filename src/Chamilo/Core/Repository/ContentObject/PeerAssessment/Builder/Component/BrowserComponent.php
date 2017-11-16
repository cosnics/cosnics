<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Translation\Translation;

class BrowserComponent extends Manager
{

    public function run()
    {
        // is context repository?
        if ($this->get_parent() instanceof \Chamilo\Core\Repository\Component\BuilderComponent &&
             $this->get_root_content_object()->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
        {
            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Builder\Action\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
        }
        else
        {
            $publication_has_scores = $this->publication_has_scores();
            // is context tool or app?

            if ($this->get_root_content_object()->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK &&
                 ! $publication_has_scores)
            {
                return $this->getApplicationFactory()->getApplication(
                    \Chamilo\Core\Repository\Builder\Action\Manager::context(),
                    new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
            }
            else
            {
                $this->redirect(null, false, array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS));
            }
        }
    }

    function render_header()
    {
        $html = array();

        $html[] = parent::render_header();
        $html[] = '<div class="context_info alert alert-warning">' . Translation::get('IndicatorInfoMessage') . '</div>';

        return implode(PHP_EOL, $html);
    }
}
