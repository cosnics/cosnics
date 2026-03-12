<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleteTemporaryFileComponent extends Manager
{
    public const string PARAM_FILE = 'file';

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Filesystem $filesystem;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        ConfigurablePathBuilder $configurablePathBuilder, Filesystem $filesystem, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);

        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->filesystem = $filesystem;
    }

    public function run(?User $currentUser = null): Response
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

    // Input parameters

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }
}