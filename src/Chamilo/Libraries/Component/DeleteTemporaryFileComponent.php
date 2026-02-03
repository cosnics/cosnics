<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleteTemporaryFileComponent extends Manager
{
    // Input parameters
    public const PARAM_FILE = 'file';

    public function run(): Response
    {
        $temporaryFileName = $this->getRequest()->getFromQueryOrRequest(self::PARAM_FILE);
        $temporaryPath = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__);
        $temporaryFilePath = $temporaryPath . $temporaryFileName;

        $translator = $this->getTranslator();

        try {
            $this->getFilesystem()->remove($temporaryFilePath);

            return JsonAjaxResult::success($translator->trans('FileRemoved', [], StringUtilities::LIBRARIES));
        }
        catch (Exception) {
            return JsonAjaxResult::generalError($translator->trans('FileNotRemoved', [], StringUtilities::LIBRARIES));
        }
    }

    /**
     * @see \Chamilo\Libraries\Protocol\Ajax\Service\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_FILE];
    }
}