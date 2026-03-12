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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UploadTemporaryFileComponent extends Manager
{
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

    /**
     * @throws \Exception
     */
    public function run(?User $currentUser = null): Response
    {
        $file = $this->getFile();

        if (!$file->isValid()) {
            return JsonAjaxResult::badRequest(
                $this->getTranslator()->trans('NoValidFileUploaded', [], StringUtilities::LIBRARIES)
            );
        }

        $temporaryPath = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__);

        $this->getFilesystem()->mkdir($temporaryPath);

        $fileName = md5(Uuid::v7()->__toString());
        $temporaryFilePath = $temporaryPath . $fileName;

        $result = move_uploaded_file($file->getRealPath(), $temporaryFilePath);

        if (!$result) {
            return JsonAjaxResult::generalError(
                $this->getTranslator()->trans('FileNotUploaded', [], StringUtilities::LIBRARIES)
            );
        }
        else {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->setProperties(['temporaryFileName' => $fileName]);

            return $jsonAjaxResult->getResponse();
        }
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @throws \Exception
     */
    public function getFile(): UploadedFile
    {
        $filePropertyName = $this->getRequest()->request->get('filePropertyName');
        if (empty($filePropertyName)) {
            throw new Exception('filePropertyName parameter not available in request');
        }

        $file = $this->getRequest()->files->get($filePropertyName);
        if (empty($file)) {
            $errorMessage = 'File with key ' . $filePropertyName . 'not found in request.';

            $availableKeys = $this->getRequest()->files->keys();
            if (!empty($availableKeys)) {
                $errorMessage .= ' Available file keys: ' . implode(', ', $availableKeys) . '.';
            }

            throw new Exception($errorMessage);
        }

        return $file;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }
}