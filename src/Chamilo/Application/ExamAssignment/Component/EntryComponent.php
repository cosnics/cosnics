<?php

namespace Chamilo\Application\ExamAssignment\Component;

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
     */
    function run()
    {
        $this->redirectToLoginIfNotAuthenticated();

        $jqueryFileUploadScriptPath =
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.file.upload.js';

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $publicationId = $this->getRequest()->getFromUrl(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);
        $code = $this->getRequest()->getFromPost(self::PARAM_CODE);

        $allowed = $this->getExamAssignmentService()->canUserViewExamAssignment(
            $this->getUser(), $publicationId, $code
        );

        $details = $this->getExamAssignmentService()->getExamAssignmentDetails($this->getUser(), $publicationId);

        $parameters = [
            'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
            'ALLOWED_TO_VIEW_ASSIGNMENT' => $allowed, 'USER' => $this->getUser(), 'DETAILS' => $details,
            'JQUERY_FILE_UPLOAD_SCRIPT_PATH' => $jqueryFileUploadScriptPath,
            'UPLOAD_URL' => $this->get_url(
                [
                    self::PARAM_CONTEXT => \Chamilo\Application\ExamAssignment\Ajax\Manager::context(),
                    self::PARAM_ACTION => \Chamilo\Application\ExamAssignment\Ajax\Manager::ACTION_UPLOAD_EXAM,
                    \Chamilo\Application\ExamAssignment\Ajax\Manager::PARAM_SECURITY_CODE => $details['security_code']
                ]
            ),
            'LOGOUT_URL' => $this->getLogoutUrl()
        ];

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(Manager::context() . ':Entry.html.twig', $parameters);
    }

}
