<?php
namespace Chamilo\Application\Survey\Form;

use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_OOCalc;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class SubscribeMailForm extends FormValidator
{
    const APPLICATION_NAME = 'survey';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_RIGHTS = 'rights';
    const IMPORT_FILE_NAME = 'context_user_file';

    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    private $parent;

    private $publication_id;

    function __construct($publication_id, $action)
    {
        parent :: __construct('subscribe_users', 'post', $action);

        $this->publication_id = $publication_id;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('category', Translation :: get('UserEmails'));
        $this->add_information_message(null, null, Translation :: get('ExcelfileWithFirstColumnOfEmails'));
        $this->addElement(
            'file',
            self :: IMPORT_FILE_NAME,
            Translation :: get('FileName', null, Utilities :: COMMON_LIBRARIES));

        $rights = RightsService :: getInstance();
        foreach ($rights as $right_name => $right)
        {
            $check_boxes[] = $this->createElement('checkbox', $right, $right_name, $right_name . '  ');
        }
        $this->addGroup($check_boxes, self :: PARAM_RIGHTS, Translation :: get('Rights'), '&nbsp;', true);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('SubscribeEmails'),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement('category');
        $this->addElement('html', '<br />');
    }

    function create_rights()
    {
        $values = $this->exportValues();
        $array = explode('.', $_FILES[self :: IMPORT_FILE_NAME]['name']);
        $type = $array[count($array) - 1];

        switch ($type)
        {
            case 'xlsx' :
                $PhpReader = new PHPExcel_Reader_Excel2007();
                $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
                break;
            case 'ods' :
                $PhpReader = new PHPExcel_Reader_OOCalc();
                $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
                break;
            case 'xls' :
                $PhpReader = new PHPExcel_Reader_Excel5();
                $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
                break;
            default :
                return false;
                break;
        }

        $worksheet = $excel->getSheet(0);
        $excel_array = $worksheet->toArray();
        $no_user_emails = array();
        $location_id = RightsService :: getInstance();

        // each row in excel file starting row1 =header
        for ($i = 1; $i < count($excel_array); $i ++)
        {

            $email = $excel_array[$i][0];
            $users = \Chamilo\Core\User\Storage\DataManager :: retrieve_users_by_email($email);
            if (count($users) > 0)
            {
                foreach ($users as $user)
                {
                    $user_id = $user->get_id();
                    foreach ($values[self :: PARAM_RIGHTS] as $right => $value)
                    {
                        if ($value == 1)
                        {
                            $succes = RightsService :: getInstance();
                        }
                    }
                }
            }
            else
            {
                $no_user_emails[] = $email;
            }
        }
        // exit;
        return $no_user_emails;
    }
}

?>