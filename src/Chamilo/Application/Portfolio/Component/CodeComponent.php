<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Application\Portfolio\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CodeComponent extends Manager
{

    public function run()
    {
        $officialCode = Request::get(self::PARAM_USER_ID);

        if (is_numeric($officialCode))
        {
            try
            {
                $user = $this->getUserService()->findUserByOfficialCode($officialCode);

                if ($user instanceof User)
                {
                    $redirect = new Redirect(
                        array(
                            self::PARAM_CONTEXT => self::package(),
                            self::PARAM_ACTION => self::ACTION_HOME,
                            self::PARAM_USER_ID => $user->get_id()));
                    $redirect->toUrl();
                }
                else
                {
                    throw new Exception(Translation::get('NoSuchUser'));
                }
            }
            catch (Exception $exception)
            {
                throw new Exception(Translation::get('NoSuchUser'));
            }
        }
        else
        {
            throw new Exception(Translation::get('NoSuchUser'));
        }
    }
}
