<?php

namespace Chamilo\Application\ExamAssignment\Component;

use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * Class EntryComponent
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class EntryComponent extends Manager
{
    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $publicationId = $this->getRequest()->getFromUrl(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);
        $code = $this->getRequest()->getFromUrl(self::PARAM_CODE);

        $allowed = $this->getExamAssignmentService()->canUserViewExamAssignment(
            $this->getUser(), $publicationId, $code
        );

        $details = $this->getExamAssignmentService()->getExamAssignmentDetails($this->getUser(), $publicationId);

        $parameters = [
            'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
            'ALLOWED_TO_VIEW_ASSIGNMENT' => $allowed, 'USER' => $this->getUser(), 'DETAILS' => $details
        ];

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(Manager::context() . ':Entry.html.twig', $parameters);
    }

}
