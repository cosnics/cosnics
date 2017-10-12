<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleteTemporaryFileComponent extends \Chamilo\Libraries\Ajax\Manager
{
    // Input parameters
    const PARAM_FILE = 'file';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_FILE);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $temporaryFileName = $this->getPostDataValue(self::PARAM_FILE);

        $temporaryPath = Path::getInstance()->getTemporaryPath(__NAMESPACE__);
        $owner = $this->getPostDataValue(\Chamilo\Core\User\Manager::PARAM_USER_USER_ID);

        $temporaryFilePath = $temporaryPath . $temporaryFileName;

        $result = Filesystem::remove($temporaryFilePath);

        if (! $result)
        {
            JsonAjaxResult::general_error(Translation::get('FileNotRemoved'));
        }
        else
        {
            JsonAjaxResult::success(Translation::get('FileRemoved'));
        }
    }
}