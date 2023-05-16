<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntryTableRenderer extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\EntryTableRenderer
{
    protected LearningPathAssignmentService $learningPathAssignmentService;

    public function __construct(
        LearningPathAssignmentService $learningPathAssignmentService, AssignmentDataProvider $assignmentDataProvider,
        DatetimeUtilities $datetimeUtilities, StringUtilities $stringUtilities, User $user, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->learningPathAssignmentService = $learningPathAssignmentService;

        parent::__construct(
            $assignmentDataProvider, $datetimeUtilities, $stringUtilities, $user, $translator, $urlGenerator,
            $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getEntryClassName(): string
    {
        return $this->getLearningPathAssignmentService()->getEntryClassName();
    }

    public function getLearningPathAssignmentService(): LearningPathAssignmentService
    {
        return $this->learningPathAssignmentService;
    }

    public function getScoreClassName(): string
    {
        return $this->getLearningPathAssignmentService()->getScoreClassName();
    }
}