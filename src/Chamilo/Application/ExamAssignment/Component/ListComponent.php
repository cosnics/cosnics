<?php

namespace Chamilo\Application\ExamAssignment\Component;

use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Application\ExamAssignment\Service\ExamAssignmentService;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * Class ListComponent
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class ListComponent extends Manager implements NoAuthenticationSupport
{
    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $this->redirectToLoginIfNotAuthenticated();

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $exams = $this->getExamAssignmentService()->getCurrentExamAssignmentsForUser($this->getUser());

        return $this->getTwig()->render(
            Manager::context() . ':List.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'USER' => $this->getUser(),
                'HEADER_ENTITY' => 'exam.hogent.be',
                'EXAMS' => $exams,
                'ENTRY_URL' => $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_ENTRY,
                        self::PARAM_CONTENT_OBJECT_PUBLICATION_ID => '__EXAM_ID__'
                    ]
                ),
                'LOGOUT_URL' => $this->getLogoutUrl()
            ]
        );
    }
}
