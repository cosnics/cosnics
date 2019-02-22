<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewReportComponent extends Manager
{
    /**
     * @return \Chamilo\Libraries\Format\Response\Response|string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $contentObjectPlagiarismResultId =
            $this->getRequest()->getFromUrl(self::PARAM_CONTENT_OBJECT_PLAGIARISM_RESULT_ID);

        if (empty($contentObjectPlagiarismResultId))
        {
            throw new NoObjectSelectedException(
                $this->getTranslator()->trans('ContentObjectPlagiarismResult', [], Manager::context())
            );
        }

        try
        {
            $viewUrl = $this->getContentObjectPlagiarismChecker()->getPlagiarismViewerUrlForContentObjectById(
                $contentObjectPlagiarismResultId, $this->get_course(), $this->getUser()
            );

            return new RedirectResponse($viewUrl);
        }
        catch (EulaNotAcceptedException $exception)
        {
            $redirectUrl = $this->get_url();

            return $this->getContentObjectPlagiarismChecker()->getRedirectToEULAPageResponse($redirectUrl);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            $this->redirect(
                $this->getTranslator()->trans('ViewReportFailed', [], Manager::context()), false,
                [self::PARAM_ACTION => self::ACTION_BROWSE]
            );
        }

        return null;
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters()
    {
        return [self::PARAM_CONTENT_OBJECT_PLAGIARISM_RESULT_ID];
    }

}