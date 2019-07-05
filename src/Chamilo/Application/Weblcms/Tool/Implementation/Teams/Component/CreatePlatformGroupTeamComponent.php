<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatePlatformGroupTeamComponent extends Manager
{
    public function run()
    {

        try
        {
            $message = 'PlatformGroupTeamCreated';
            $success = true;
        }
        catch(UserException $ex)
        {
            throw $ex;
        }
        catch(\Exception $ex)
        {
            $message = 'PlatformGroupTeamNotCreated';
            $success = false;
        }

        $this->redirect($this->getTranslator()->trans($message, [], Manager::context()), !$success, []);

    }
}