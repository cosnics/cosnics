<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Chamilo\Libraries\File\Properties\FileProperties;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class FormProcessor
{

    /**
     *
     * @var integer
     */
    private $userIdentifier;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    /**
     *
     * @var string[]
     */
    private $formValues;

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param string[] $formValues
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct($userIdentifier, WorkspaceInterface $workspace, $formValues, ChamiloRequest $request)
    {
        $this->userIdentifier = $userIdentifier;
        $this->workspace = $workspace;
        $this->formValues = $formValues;
        $this->request = $request;
    }

    /**
     *
     * @return number
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     *
     * @param number $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     */
    public function setWorkspace(WorkspaceInterface $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     *
     * @return string[]
     */
    public function getFormValues()
    {
        return $this->formValues;
    }

    /**
     *
     * @param string[] $formValues
     */
    public function setFormValues($formValues)
    {
        $this->formValues = $formValues;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @throws \Exception
     * @return integer
     */
    public function determineCategoryIdentifier()
    {
        $formValues = $this->getFormValues();
        
        $parentIdentifier = $formValues[ContentObject::PROPERTY_PARENT_ID];
        $newCategoryName = $formValues[ContentObjectImportForm::NEW_CATEGORY];
        
        if (! StringUtilities::getInstance()->isNullOrEmpty($newCategoryName, true))
        {
            $newCategory = new RepositoryCategory();
            
            $newCategory->set_name($newCategoryName);
            $newCategory->set_parent($parentIdentifier);
            $newCategory->set_type_id($this->getWorkspace()->getId());
            $newCategory->set_type($this->getWorkspace()->getWorkspaceType());
            
            if (! $newCategory->create())
            {
                throw new \Exception(Translation::get('CategoryCreationFailed'));
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
     *
     * @return \Chamilo\Core\Repository\Common\Import\ImportParameters
     */
    abstract public function getImportParameters();

    /**
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return \Chamilo\Libraries\File\Properties\FileProperties
     */
    public function getFileProperties(UploadedFile $file)
    {
        $fileProperties = new FileProperties();
        $fileProperties->set_extension($file->getClientOriginalExtension());
        $fileProperties->set_name($file->getClientOriginalName());
        $fileProperties->set_type($file->getMimeType());
        $fileProperties->set_size($file->getSize());
        $fileProperties->set_path($file->getRealPath());

        return $fileProperties;
    }

    /**
     *
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFileByName($fileName = ContentObjectImportForm::IMPORT_FILE_NAME)
    {
        return $this->getRequest()->files->get($fileName);
    }

    /**
     *
     * @param string $fileName
     * @return \Chamilo\Libraries\File\Properties\FileProperties|NULL
     */
    public function getFile($fileName = ContentObjectImportForm::IMPORT_FILE_NAME)
    {
        $file = $this->getFileByName($fileName);
        var_dump($file);
        if ($file instanceof UploadedFile)
        {
            return $this->getFileProperties($file);
        }
        else
        {
            return null;
        }
    }
}