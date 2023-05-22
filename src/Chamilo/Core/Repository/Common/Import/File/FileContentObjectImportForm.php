<?php
namespace Chamilo\Core\Repository\Common\Import\File;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;

class FileContentObjectImportForm extends ContentObjectImportForm
{
    public const DOCUMENT_LINK = 1;

    public const DOCUMENT_UPLOAD = 0;

    public const PARAM_DOCUMENT_TYPE = 'document_type';

    public const PARAM_WORKSPACE_ID = 'workspace_id';

    public const PROPERTY_LINK = 'url';

    public function build_basic_form()
    {
        parent::build_basic_form();

        $this->addElement(
            'radio', self::PARAM_DOCUMENT_TYPE, Translation::get('File'), Translation::get('Upload'),
            self::DOCUMENT_UPLOAD
        );

        $workspaceId = $this->importFormParameters->getWorkspace()->getId();

        $this->addElement('hidden', self::PARAM_WORKSPACE_ID, $workspaceId, ['id' => 'workspace_id']);

        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_upload">');

        $calculator = new Calculator(
            DataManager::retrieve_by_id(
                User::class, (int) $this->get_application()->get_user_id()
            )
        );

        $uploadUrl = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::CONTEXT,
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
            ]
        );

        $dropZoneParameters = [
            'name' => self::IMPORT_FILE_NAME,
            'maxFilesize' => $calculator->getMaximumUploadSize(),
            'uploadUrl' => $uploadUrl->getUrl(),
            'successCallbackFunction' => 'chamilo.core.repository.import.processUploadedFile',
            'sendingCallbackFunction' => 'chamilo.core.repository.import.prepareRequest',
            'removedfileCallbackFunction' => 'chamilo.core.repository.import.deleteUploadedFile'
        ];

        if (!$this->importFormParameters->canUploadMultipleFiles())
        {
            $dropZoneParameters['maxFiles'] = 1;
        }

        $this->addFileDropzone(self::IMPORT_FILE_NAME, $dropZoneParameters, false);

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(Manager::CONTEXT) . 'Plugin/jquery.file.upload.import.js'
        )
        );

        $this->addElement('html', '</div>');

        $this->addElement(
            'radio', self::PARAM_DOCUMENT_TYPE, null,
            Translation::getInstance()->getTranslation('ImportFromLink', null, Manager::CONTEXT), self::DOCUMENT_LINK
        );

        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_link">');
        $this->add_textfield(self::PROPERTY_LINK, null, false);
        $this->addElement('html', '</div>');
    }

    /**
     * @return bool
     */
    protected function implementsDropZoneSupport()
    {
        return true;
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        parent::setDefaults(
            [
                self::PARAM_DOCUMENT_TYPE => self::DOCUMENT_UPLOAD,
                self::PROPERTY_TYPE => ContentObjectImport::FORMAT_FILE,
                self::PROPERTY_LINK => 'http://'
            ]
        );
    }
}
