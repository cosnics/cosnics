<?php
namespace Chamilo\Core\Repository\Common\Import\Factory;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportFormParameters;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * Interface ImportFactoryInterface
 *
 * @package Chamilo\Core\Repository\Common\Import\Factory
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface ImportFactoryInterface
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
        int $userId, WorkspaceInterface $workspace, int $categoryId = 0, $file = null, $form_values = array()
    );

    /**
     * @param ImportFormParameters $importFormParameters
     *
     * @return ContentObjectImportForm
     *
     * @throws \Exception
     */
    public function getImportForm(ImportFormParameters $importFormParameters);

    /**
     * @param ImportParameters $importParameters
     *
     * @return ContentObjectImportController
     */
    public function getImportController(ImportParameters $importParameters);

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
    );

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * Returns the application / context that houses this importer
     *
     * @return string
     */
    public function getImportContext();
}
