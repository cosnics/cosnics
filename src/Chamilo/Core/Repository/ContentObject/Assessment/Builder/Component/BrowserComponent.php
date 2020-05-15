<?php

namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Selector\Option\LinkTypeSelectorOption;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 *
 * @package repository.lib.complex_builder.assessment.component
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Builder\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    /*
     * (non-PHPdoc) @see \core\repository\builder\Manager::get_additional_links()
     */
    public function get_additional_links()
    {
        $links = array();

        $links[] = new LinkTypeSelectorOption(
            self::package(),
            'MergeAssessment',
            $this->get_url(
                array(\Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => self::ACTION_MERGE_ASSESSMENT)
            )
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(),
            'SelectQuestions',
            $this->get_url(
                array(
                    \Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER
                )
            )
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(),
            'SelectQuestionsInWorkspaces',
            $this->get_url(
                array(
                    \Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER,
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES => 1
                )
            )
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(),
            'RandomizeQuestionOptions',
            $this->get_url(array(\Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => self::ACTION_RANDOMIZE))
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(),
            'AnswerFeedbackType',
            $this->get_url(
                array(\Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => self::ACTION_ANSWER_FEEDBACK_TYPE)
            )
        );

        return $links;
    }
}
