<?php

namespace Chamilo\Core\Repository\Common\Import\File;

use Chamilo\Core\Repository\Common\Import\Factory\RepositoryImportFactory;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\Factory\ImportFactoryInterface;
use Chamilo\Core\Repository\Common\Import\ImportFormParameters;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * Class FileImportFactory
 * @package Chamilo\Core\Repository\Common\Import\File
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class FileImportFactory extends RepositoryImportFactory implements ImportFactoryInterface
{
    /**
     * @param int $userId
     * @param WorkspaceInterface $workspace
     * @param int $categoryId
     * @param null $file
     * @param array $form_values
     *
     * @return ImportParameters
     */
    public function getImportParameters(
        int $userId, WorkspaceInterface $workspace, int $categoryId = 0, $file = null,
        $form_values = array()
    )
    {
        return new FileImportParameters('file', $userId, $workspace, $categoryId, $file, $form_values);
    }

    /**
     * @param ImportFormParameters $importFormParameters
     *
     * @return ContentObjectImportForm
     *
     * @throws \Exception
     */
    public function getImportForm(ImportFormParameters $importFormParameters)
    {
        return new FileContentObjectImportForm($importFormParameters);
    }

    /**
     * @param ImportParameters $importParameters
     *
     * @return ContentObjectImportController
     */
    public function getImportController(ImportParameters $importParameters)
    {
        return new FileContentObjectImportController($importParameters);
    }

    /**
     * @param int $userIdentifier
     * @param WorkspaceInterface $workspace
     * @param $formValues
     * @param ChamiloRequest $request
     *
     * @return \Chamilo\Core\Repository\Common\Import\FormProcessor
     */
    public function getImportFormProcessor(
        int $userIdentifier, WorkspaceInterface $workspace, $formValues, ChamiloRequest $request
    )
    {
        return new FormProcessor($userIdentifier, $workspace, $formValues, $request);
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return FileContentObjectImportController::is_available();
    }
}
