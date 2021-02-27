<?php

namespace Chamilo\Application\ExamAssignment\Component;

use Chamilo\Application\ExamAssignment\Domain\AssignmentViewStatus;
use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * Class EntryComponent
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class EntryComponent extends Manager implements NoAuthenticationSupport
{
    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    function run()
    {
        $this->redirectToLoginIfNotAuthenticated();

        $jqueryFileUploadScriptPath =
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.file.upload.js';

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $publicationId = $this->getRequest()->getFromUrl(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);
        $code = $this->getRequest()->getFromPost(self::PARAM_CODE);
        $securityCode = $this->getRequest()->getFromUrl(self::PARAM_SECURITY_CODE);

        $assignmentViewStatus = $this->getExamAssignmentService()->getAssignmentViewStatusForUser(
            $this->getUser(), $publicationId, $code, $securityCode
        );

        $details = $this->getExamAssignmentService()->getExamAssignmentDetails($this->getUser(), $publicationId);

        $parameters = [
            'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
            'HEADER_ENTITY' => $this->getBrandTitle(),
            'ALLOWED_TO_VIEW_ASSIGNMENT' => $assignmentViewStatus->isAllowed(),
            'ASSIGNMENT_VIEW_STATUS' => $assignmentViewStatus->getStatus(),
            'USER' => $this->getUser(), 'DETAILS' => $details,
            'JQUERY_FILE_UPLOAD_SCRIPT_PATH' => $jqueryFileUploadScriptPath,
            'UPLOAD_URL' => $this->get_url(
                [
                    self::PARAM_CONTEXT => \Chamilo\Application\ExamAssignment\Ajax\Manager::context(),
                    self::PARAM_ACTION => \Chamilo\Application\ExamAssignment\Ajax\Manager::ACTION_UPLOAD_EXAM,
                    \Chamilo\Application\ExamAssignment\Ajax\Manager::PARAM_SECURITY_CODE => $details['security_code']
                ]
            ),
            'LOGOUT_URL' => $this->getLogoutUrl(),
            'LIST_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_LIST]),
            'CURRENT_URL' => $this->get_url(
                [
                    self::PARAM_CONTENT_OBJECT_PUBLICATION_ID => $publicationId,
                    self::PARAM_SECURITY_CODE => $details['security_code']
                ]
            ),
            'RETRY_MODE' => $this->getRequest()->getFromUrl(self::PARAM_RETRY) == 1
        ];

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(Manager::context() . ':Entry2.html.twig', $parameters);
    }

}
