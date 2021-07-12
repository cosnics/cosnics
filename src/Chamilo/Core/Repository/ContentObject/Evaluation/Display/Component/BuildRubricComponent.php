<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BuildRubricComponent extends Manager
{
    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation())
        {
            throw new NotAllowedException();
        }

        return $this->runRubricComponent('Builder', false);
    }

    public function render_header($pageTitle = null) {
        $translator = Translation::getInstance();
        return parent::render_header($pageTitle) .
            '<div class="rubric-back-to-assignment"><a href="' .
            $this->get_url([self::PARAM_ACTION => self::DEFAULT_ACTION]) .
            '" target="_self" style=""><i class="fa fa-arrow-left"></i>' .
            $translator->getTranslation('ReturnToEvaluation') .
            '</a></div>';
    }
}
