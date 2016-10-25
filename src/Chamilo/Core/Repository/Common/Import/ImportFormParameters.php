<?php

namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * Parameters for the import form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportFormParameters
{
    const IMPORT_MULTIPLE_FILES = 0;
    const IMPORT_SINGLE_FILE = 1;

    /**
     * @var string
     */
    protected $importFormType;

    /**
     * @var WorkspaceInterface
     */
    protected $workspace;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string (POST OR GET)
     */
    protected $method;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var bool
     */
    protected $showCategories;

    /**
     * @var int
     */
    protected $maximumFilesToUpload;

    /**
     * ImportFormParameters constructor.
     *
     * @param string $importFormType
     * @param WorkspaceInterface $workspace
     * @param Application $application
     * @param string $method
     * @param string $action
     * @param bool $showCategories
     * @param int $maximumFilesToUpload
     */
    public function __construct(
        $importFormType, WorkspaceInterface $workspace, Application $application, $action, $method = 'post',
        $showCategories = true,
        $maximumFilesToUpload = self::IMPORT_MULTIPLE_FILES
    )
    {
        $this->importFormType = $importFormType;
        $this->workspace = $workspace;
        $this->application = $application;
        $this->method = $method;
        $this->action = $action;
        $this->showCategories = $showCategories;

        if (!in_array($maximumFilesToUpload, array(self::IMPORT_MULTIPLE_FILES, self::IMPORT_SINGLE_FILE)))
        {
            throw new \InvalidArgumentException(
                'The given parameter "maximumFilesToUpload" must be either ImportParameters::IMPORT_MULTIPLE_FILES' .
                ' (0) or ImportParameters::IMPORT_SINGLE_FILE (1)'
            );
        }

        $this->maximumFilesToUpload = $maximumFilesToUpload;
    }

    /**
     * @return string
     */
    public function getImportFormType()
    {
        return $this->importFormType;
    }

    /**
     * @return WorkspaceInterface
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return boolean
     */
    public function isShowCategories()
    {
        return $this->showCategories;
    }

    /**
     * @return int
     */
    public function getMaximumFilesToUpload()
    {
        return $this->maximumFilesToUpload;
    }

    /**
     * Helper method to return whether or not the import form can import multiple files
     *
     * @return bool
     */
    public function canUploadMultipleFiles()
    {
        return $this->maximumFilesToUpload == self::IMPORT_MULTIPLE_FILES;
    }
}