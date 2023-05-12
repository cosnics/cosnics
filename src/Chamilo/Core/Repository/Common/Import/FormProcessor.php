<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Core\Repository\Common\Import
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class FormProcessor
{

    /**
     * @var string[]
     */
    private $formValues;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     * @var int
     */
    private $userIdentifier;

    private Workspace $workspace;

    /**
     * @param int $userIdentifier
     * @param string[] $formValues
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct($userIdentifier, Workspace $workspace, $formValues, ChamiloRequest $request)
    {
        $this->userIdentifier = $userIdentifier;
        $this->workspace = $workspace;
        $this->formValues = $formValues;
        $this->request = $request;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function determineCategoryIdentifier()
    {
        $formValues = $this->getFormValues();

        $parentIdentifier = $formValues[ContentObject::PROPERTY_PARENT_ID];
        $newCategoryName = $formValues[ContentObjectImportForm::NEW_CATEGORY];

        if (!StringUtilities::getInstance()->isNullOrEmpty($newCategoryName, true))
        {
            $newCategory = new RepositoryCategory();

            $newCategory->set_name($newCategoryName);
            $newCategory->set_parent($parentIdentifier);
            $newCategory->set_type_id($this->getWorkspace()->getId());
            $newCategory->setType($this->getWorkspace()->getWorkspaceType());

            if (!$newCategory->create())
            {
                throw new Exception(Translation::get('CategoryCreationFailed'));
            }
            else
            {
                $categoryIdentifier = $newCategory->getId();
            }
        }
        else
        {
            $categoryIdentifier = $parentIdentifier;
        }

        return $categoryIdentifier;
    }

    /**
     * @param string $fileName
     *
     * @return \Chamilo\Libraries\File\Properties\FileProperties|NULL
     */
    public function getFile($fileName = ContentObjectImportForm::IMPORT_FILE_NAME)
    {
        $file = $this->getFileByName($fileName);

        if ($file instanceof UploadedFile)
        {
            return $this->getFileProperties($file);
        }
        else
        {
            return null;
        }
    }

    /**
     * @param string $fileName
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFileByName($fileName = ContentObjectImportForm::IMPORT_FILE_NAME)
    {
        return $this->getRequest()->files->get($fileName);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Chamilo\Libraries\File\Properties\FileProperties
     */
    public function getFileProperties(UploadedFile $file)
    {
        $fileProperties = new FileProperties();
        $fileProperties->set_extension($file->getClientOriginalExtension());
        $fileProperties->set_name($file->getClientOriginalName());
        $fileProperties->setType($file->getMimeType());
        $fileProperties->set_size($file->getSize());
        $fileProperties->set_path($file->getRealPath());

        return $fileProperties;
    }

    /**
     * @return string[]
     */
    public function getFormValues()
    {
        return $this->formValues;
    }

    /**
     * @return \Chamilo\Core\Repository\Common\Import\ImportParameters
     */
    abstract public function getImportParameters();

    /**
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return number
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    /**
     * @param string[] $formValues
     */
    public function setFormValues($formValues)
    {
        $this->formValues = $formValues;
    }

    /**
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param number $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }

    public function setWorkspace(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }
}