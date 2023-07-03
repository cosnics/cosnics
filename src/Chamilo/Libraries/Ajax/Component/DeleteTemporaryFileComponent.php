<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Libraries\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleteTemporaryFileComponent extends Manager
{
    // Input parameters
    public const PARAM_FILE = 'file';

    /**
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $temporaryFileName = $this->getPostDataValue(self::PARAM_FILE);
        $temporaryPath = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__);
        $temporaryFilePath = $temporaryPath . $temporaryFileName;

        $translator = $this->getTranslator();

        try
        {
            $this->getFilesystem()->remove($temporaryFilePath);
            JsonAjaxResult::success($translator->trans('FileRemoved', [], StringUtilities::LIBRARIES));
        }
        catch (Exception)
        {
            JsonAjaxResult::general_error($translator->trans('FileNotRemoved', [], StringUtilities::LIBRARIES));
        }
    }

    /**
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_FILE];
    }
}