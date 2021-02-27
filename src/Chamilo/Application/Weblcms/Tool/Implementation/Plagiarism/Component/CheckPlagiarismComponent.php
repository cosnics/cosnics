<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CheckPlagiarismComponent extends Manager
{
    /**
     * @return \Chamilo\Libraries\Format\Response\Response|string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        if($this->getContentObjectPlagiarismChecker()->isInMaintenanceMode())
        {
           throw new NotAllowedException();
        }

        if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }

        try
        {
            $objectIds = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects($this->getUser());
            if (! is_array($objectIds))
            {
                $objectIds = array($objectIds);
            }

            $this->getContentObjectPlagiarismChecker()->checkContentObjectsForPlagiarismById(
                $this->get_course(), $objectIds, $this->getUser()
            );

            $message = 'PlagiarismCheckSuccess';
            $success = true;
        }
        catch (EulaNotAcceptedException $exception)
        {
            $redirectUrl = $this->get_url();

            return $this->getContentObjectPlagiarismChecker()->getRedirectToEULAPageResponse($redirectUrl);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            $message = 'PlagiarismCheckFailed';
            $success = false;
        }

        $this->redirect($message, !$success, [self::PARAM_ACTION => self::ACTION_BROWSE]);

        return null;
    }

    /**
     * @return array
     */
    public function get_allowed_content_object_types()
    {
        return [File::class];
    }

}
