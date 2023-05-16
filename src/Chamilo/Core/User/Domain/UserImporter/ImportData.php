<?php
namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the data for a single import
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ImportData
{
    public const ACTION_ADD = 'A';
    public const ACTION_UPDATE = 'U';
    public const ACTION_ADD_UPDATE = 'UA';
    public const ACTION_DELETE = 'D';

    /**
     * The imported data as a raw string. It is used to give the user the opportunity to retry the failed imports.
     *
     * @var string
     */
    protected $rawImportData;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var \Chamilo\Core\User\Domain\UserImporter\ImportDataResult
     */
    protected $importDataResult;

    /**
     * ImportData constructor.
     *
     * @param string $rawImportData
     * @param string $action
     */
    public function __construct($rawImportData, $action)
    {
        $this->rawImportData = $rawImportData;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getRawImportData()
    {
        return $this->rawImportData;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns whether or not this user should be created as a new user
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getAction() == self::ACTION_ADD;
    }

    /**
     * Returns whether or not this user should be created if the username is not found or updated if the
     * username is found
     *
     * @return bool
     */
    public function isNewOrUpdate()
    {
        return $this->getAction() == self::ACTION_ADD_UPDATE;
    }

    /**
     * Returns whether or not this user should be updated
     *
     * @return bool
     */
    public function isUpdate()
    {
        return $this->getAction() == self::ACTION_UPDATE;
    }

    /**
     * Returns whether or not this user should be deleted
     *
     * @return bool
     */
    public function isDelete()
    {
        return $this->getAction() == self::ACTION_DELETE;
    }

    /**
     * Returns whether or not this imported user has a valid action
     *
     * @return bool
     */
    public function hasValidAction()
    {
        return in_array($this->getAction(), $this->getValidActions());
    }

    /**
     * Sets the action to new
     */
    public function setActionToNew()
    {
        $this->setAction(self::ACTION_ADD);
    }

    /**
     * Sets the action to update
     */
    public function setActionToUpdate()
    {
        $this->setAction(self::ACTION_UPDATE);
    }

    /**
     * @param string $rawImportData
     *
     * @return $this
     */
    public function setRawImportData($rawImportData)
    {
        $this->rawImportData = $rawImportData;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return \Chamilo\Core\User\Domain\UserImporter\ImportDataResult
     */
    public function getImportDataResult(): ImportDataResult
    {
        return $this->importDataResult;
    }

    /**
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportDataResult $importDataResult
     *
     * @return ImportData
     */
    public function setImportDataResult(ImportDataResult $importDataResult)
    {
        $this->importDataResult = $importDataResult;

        return $this;
    }

    /**
     * Returns the list of valid actions
     *
     * @return array
     */
    abstract public function getValidActions();
}