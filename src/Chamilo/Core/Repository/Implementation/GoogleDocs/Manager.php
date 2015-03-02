<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'google_docs';
    const PARAM_EXPORT_FORMAT = 'export_format';
    const PARAM_FOLDER = 'folder';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#support_sorting_direction()
     */
    public function support_sorting_direction()
    {
        return false;
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     * @return string
     */
    public function get_external_repository_object_viewing_url($object)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

        return $this->get_url($parameters);
    }

    /**
     *
     * @return array
     */
    public function get_menu_items()
    {
        if ($this->get_external_repository()->get_user_setting('session_token'))
        {
            $menu_items = array();

            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';

            // Basic list of all documents
            $all_items = array();
            $all_items['title'] = Translation :: get('AllItems');
            $all_items['url'] = $this->get_url(array(self :: PARAM_FOLDER => null));
            $all_items['class'] = 'home';

            // Special lists of documents
            $owned = array();
            $owned['title'] = Translation :: get('OwnedByMe');
            $owned['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_OWNED));
            $owned['class'] = 'user';

            $viewed = array();
            $viewed['title'] = Translation :: get('OpenedByMe');
            $viewed['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_VIEWED));
            $viewed['class'] = 'userview';

            $shared = array();
            $shared['title'] = Translation :: get('SharedWithMe');
            $shared['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_SHARED));
            $shared['class'] = 'external_repository';

            $starred = array();
            $starred['title'] = Translation :: get('Starred');
            $starred['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_STARRED));
            $starred['class'] = 'template';

            $hidden = array();
            $hidden['title'] = Translation :: get('Hidden');
            $hidden['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_HIDDEN));
            $hidden['class'] = 'hidden';

            $trashed = array();
            $trashed['title'] = Translation :: get('Trash');
            $trashed['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_TRASH));
            $trashed['class'] = 'trash';

            // Document types
            $types = array();
            $types['title'] = Translation :: get('DocumentTypes');
            $types['url'] = '#';
            $types['class'] = 'category';
            $types['sub'] = array();

            $pdfs = array();
            $pdfs['title'] = Translation :: get('PDFs');
            $pdfs['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_FILES));
            $pdfs['class'] = 'google_docs_pdf';
            $types['sub'][] = $pdfs;

            $documents = array();
            $documents['title'] = Translation :: get('Documents');
            $documents['url'] = $this->get_url(array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_DOCUMENTS));
            $documents['class'] = 'google_docs_document';
            $types['sub'][] = $documents;

            $presentations = array();
            $presentations['title'] = Translation :: get('Presentations');
            $presentations['url'] = $this->get_url(
                array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_PRESENTATIONS));
            $presentations['class'] = 'google_docs_presentation';
            $types['sub'][] = $presentations;

            $spreadsheets = array();
            $spreadsheets['title'] = Translation :: get('Spreadsheets');
            $spreadsheets['url'] = $this->get_url(
                array(self :: PARAM_FOLDER => DataConnector :: DOCUMENTS_SPREADSHEETS));
            $spreadsheets['class'] = 'google_docs_spreadsheet';
            $types['sub'][] = $spreadsheets;

            $menu_items[] = $all_items;
            $menu_items[] = $line;

            $menu_items[] = $owned;
            $menu_items[] = $viewed;
            $menu_items[] = $shared;
            $menu_items[] = $starred;
            $menu_items[] = $hidden;
            $menu_items[] = $trashed;
            $menu_items[] = $types;

            // User defined folders
            $menu_items[] = $line;
            $folders = $this->get_external_repository_manager_connector()->retrieve_folders(
                $this->get_url(array(self :: PARAM_FOLDER => '__PLACEHOLDER__')));
            $menu_items = array_merge($menu_items, $folders);

            return $menu_items;
        }

        else
        {
            return $this->display_warning_page(Translation :: get('YouMustBeLoggedIn'));
        }
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#is_ready_to_be_used()
     */
    public function is_ready_to_be_used()
    {
        return false;
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryManager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
        if ($this->get_external_repository()->get_user_setting('session_token'))
        {
            $actions[] = self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY;
        }

        if (! $this->get_external_repository()->get_user_setting('session_token'))
        {
            $actions[] = self :: ACTION_LOGIN;
        }
        else
        {
            $actions[] = self :: ACTION_LOGOUT;
        }
        return $actions;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
        $document_conditions = array();
        $document_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_FILENAME),
            '*.doc',
            File :: get_type_name());
        $document_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_FILENAME),
            '*.xls',
            File :: get_type_name());
        $document_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_FILENAME),
            '*.ppt',
            File :: get_type_name());

        return new OrCondition($document_conditions);
    }

    /**
     *
     * @param $object ExternalObject
     * @return array
     */
    public function get_external_repository_object_actions(\Chamilo\Core\Repository\External\ExternalObject $object)
    {
        $actions = parent :: get_external_repository_object_actions($object);
        if (in_array(Manager :: ACTION_IMPORT_EXTERNAL_REPOSITORY, array_keys($actions)))
        {
            unset($actions[Manager :: ACTION_IMPORT_EXTERNAL_REPOSITORY]);
            $export_types = $object->get_export_types();

            foreach ($export_types as $export_type)
            {
                $actions[$export_type] = new ToolbarItem(
                    Translation :: get(
                        'Import' . StringUtilities :: getInstance()->createString($export_type)->upperCamelize()),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Import/' . $export_type),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_IMPORT_EXTERNAL_REPOSITORY,
                            self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(),
                            self :: PARAM_EXPORT_FORMAT => $export_type)),
                    ToolbarItem :: DISPLAY_ICON);
            }
        }

        return $actions;
    }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }
}
