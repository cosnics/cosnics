<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Import;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

set_time_limit(0);
ini_set('memory_limit', - 1);

/**
 * @package Chamilo\Core\User\Form
 */
class UserImportForm extends FormValidator
{
    use DependencyInjectionContainerTrait;

    public const TYPE_IMPORT = 1;

    private $current_tag;

    private $current_value;

    private $failedcsv;

    private $form_user;

    private $udm;

    private $user;

    // Constants

    private $users;

    /**
     * Creates a new UserImportForm Used to import users from a file
     */
    public function __construct($form_type, $action, $form_user)
    {
        parent::__construct('user_import', self::FORM_METHOD_POST, $action);

        $this->form_user = $form_user;
        $this->form_type = $form_type;
        $this->failedcsv = [];
        if ($this->form_type == self::TYPE_IMPORT)
        {
            $this->build_importing_form();
        }
    }

    public function build_importing_form()
    {
        $this->addElement('file', 'file', Translation::get('FileName'));
        $allowed_upload_types = ['xml', 'csv'];
        $this->addRule('file', Translation::get('OnlyCSVAllowed'), 'filetype', $allowed_upload_types);

        $group = [];
        $group[] = $this->createElement(
            'radio', 'send_mail', null, Translation::get('ConfirmYes', null, StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', 'send_mail', null, Translation::get('ConfirmNo', null, StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'mail', Translation::get('SendMailToNewUser'), '');

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Import', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('import')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $defaults['mail']['send_mail'] = 1;
        $this->setDefaults($defaults);
    }

    /**
     * XML-parser: handle character data
     */
    public function character_data($parser, $data)
    {
        $this->current_value .= $data;
    }

    public function count_failed_items()
    {
        return count($this->failedcsv);
    }

    /**
     * @param $csvuser
     *
     * @return string
     */
    protected function determineRealAction($csvuser)
    {
        $user = DataManager::retrieve_user_by_username($csvuser[User::PROPERTY_USERNAME]);

        if ($user instanceof User)
        {
            return 'U';
        }

        return 'A';
    }

    /**
     * XML-parser: handle end of element
     */
    public function element_end($parser, $data)
    {
        switch ($data)
        {
            case 'Contact' :
                if ($this->user['Status'] == '5')
                {
                    $this->user['Status'] = 5;
                }
                if ($this->user['Status'] == '1')
                {
                    $this->user['Status'] = 1;
                }
                $this->users[] = $this->user;
                break;
            case 'item' :
                $this->users[] = $this->user;
                break;
            default :
                $this->user[$data] = trim($this->current_value);
                break;
        }
        $this->current_value = '';

        // the xml_parse function splits the data in an element on special characters (for each split a different call
        // to character_data function).
        // So in the character_data function the data needs to be concatinated.
        // If an element_end is reached, the current_value needs to be reset! (otherwise the data keeps concatinating)
    }

    public function element_start($parser, $data)
    {
        switch ($data)
        {
            case 'Contact' :
                $this->user = [];
                break;
            default :
                $this->current_tag = $data;
        }
    }

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    /**
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }

    public function get_failed_csv()
    {
        // return implode($this->failedcsv, '<br />');
        $short_list = array_chunk($this->failedcsv, 20);
        $nr_more_errors = count($this->failedcsv) - 20;
        if ($nr_more_errors > 0)
        {
            $short_list[0][] = Translation::get('NrMoreInvalidRecords', ['NR' => $nr_more_errors]);
        }

        return implode($short_list[0], '<br />');
    }

    public function import_users()
    {
        $values = $this->exportValues();

        $csvusers = $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
        $validusers = [];

        $failures = 0;
        foreach ($csvusers as $csvuser)
        {
            $validuser = $this->validate_data($csvuser);

            if (!$validuser)
            {
                $failures ++;
                $this->failedcsv[] = Translation::get('Invalid') . ': ' . implode($csvuser, ';');
            }
            else
            {
                $validusers[] = $validuser;
            }
        }

        if ($failures > 0)
        {
            return false;
        }

        foreach ($validusers as $csvuser)
        {
            $action = strtoupper($csvuser['action']);

            if ($action == 'A')
            {
                $user = new User();

                $user->set_firstname($csvuser[User::PROPERTY_FIRSTNAME]);
                $user->set_lastname($csvuser[User::PROPERTY_LASTNAME]);
                $user->set_username($csvuser[User::PROPERTY_USERNAME]);

                $password = $csvuser[User::PROPERTY_PASSWORD];
                if (!$password || $password == '')
                {
                    $password = uniqid();
                }

                $user->set_password($this->getHashingUtilities()->hashString($password));

                $user->set_email($csvuser[User::PROPERTY_EMAIL]);
                $user->set_status($csvuser[User::PROPERTY_STATUS]);
                $user->set_active($csvuser[User::PROPERTY_ACTIVE]);
                $user->set_official_code($csvuser[User::PROPERTY_OFFICIAL_CODE]);
                $user->set_phone($csvuser[User::PROPERTY_PHONE]);
                $user->set_auth_source($csvuser[User::PROPERTY_AUTH_SOURCE]);

                $act_date = $csvuser[User::PROPERTY_ACTIVATION_DATE];
                if ($act_date != 0)
                {
                    $act_date = DatetimeUtilities::getInstance()->timeFromDatepicker($act_date);
                }

                $user->set_activation_date($act_date);

                $exp_date = $csvuser[User::PROPERTY_EXPIRATION_DATE];
                if ($exp_date != 0)
                {
                    $exp_date = DatetimeUtilities::getInstance()->timeFromDatepicker($exp_date);
                }

                $user->set_expiration_date($exp_date);

                $user->set_platformadmin(0);

                if (!$user->create())
                {
                    $failures ++;
                    $this->failedcsv[] = Translation::get('CreateFailed') . ': ' . implode($csvuser, ';');
                }
                else
                {
                    $this->getUserSettingService()->createUserSettingForSettingAndUser(
                        'Chamilo\Core\Admin', 'platform_language', $user, $csvuser['language']
                    );

                    $send_mail = intval($values['mail']['send_mail']);
                    if ($send_mail)
                    {
                        $this->send_email($user, $password);
                    }

                    Event::trigger(
                        'Import', Manager::CONTEXT,
                        ['target_user_id' => $user->get_id(), 'action_user_id' => $this->form_user->get_id()]
                    );
                }
            }
            elseif ($action == 'U')
            {
                $user = DataManager::retrieve_user_by_username(
                    $csvuser[User::PROPERTY_USERNAME]
                );

                if (array_key_exists(User::PROPERTY_FIRSTNAME, $csvuser))
                {
                    $user->set_firstname($csvuser[User::PROPERTY_FIRSTNAME]);
                }

                if (array_key_exists(User::PROPERTY_LASTNAME, $csvuser))
                {
                    $user->set_lastname($csvuser[User::PROPERTY_LASTNAME]);
                }

                if (array_key_exists(User::PROPERTY_EMAIL, $csvuser))
                {
                    $user->set_email($csvuser[User::PROPERTY_EMAIL]);
                }

                $user->set_status($csvuser[User::PROPERTY_STATUS]);
                $user->set_active($csvuser[User::PROPERTY_ACTIVE]);

                if (array_key_exists(User::PROPERTY_OFFICIAL_CODE, $csvuser))
                {
                    $user->set_official_code($csvuser[User::PROPERTY_OFFICIAL_CODE]);
                }

                if (array_key_exists(User::PROPERTY_PHONE, $csvuser))
                {
                    $user->set_phone($csvuser[User::PROPERTY_PHONE]);
                }

                if (array_key_exists(User::PROPERTY_AUTH_SOURCE, $csvuser))
                {
                    $user->set_auth_source($csvuser[User::PROPERTY_AUTH_SOURCE]);
                }

                $act_date = $csvuser[User::PROPERTY_ACTIVATION_DATE];
                if ($act_date != 0)
                {
                    $act_date = DatetimeUtilities::getInstance()->timeFromDatepicker($act_date);
                }

                $user->set_activation_date($act_date);

                $exp_date = $csvuser[User::PROPERTY_EXPIRATION_DATE];
                if ($exp_date != 0)
                {
                    $exp_date = DatetimeUtilities::getInstance()->timeFromDatepicker($exp_date);
                }

                $user->set_expiration_date($exp_date);

                $pass = $csvuser[User::PROPERTY_PASSWORD];
                if ($pass)
                {
                    $pass = $this->getHashingUtilities()->hashString($pass);
                    $user->set_password($pass);
                }

                if (!$user->update())
                {
                    $failures ++;
                    $this->failedcsv[] = Translation::get('UpdateFailed') . ': ' . implode($csvuser, ';');
                }
                else
                {
                    $this->getUserSettingService()->createUserSettingForSettingAndUser(
                        'Chamilo\Core\Admin', 'platform_language', $user, $csvuser['language']
                    );
                }
            }
            elseif ($action == 'D')
            {
                $user = DataManager::retrieve_user_by_username(
                    $csvuser[User::PROPERTY_USERNAME]
                );

                $user->set_active(0);

                if (!$user->update())
                {
                    $failures ++;
                    $this->failedcsv[] = Translation::get('DeleteFailed') . ': ' . implode($csvuser, ';');
                }
            }
        }

        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function parse_file($file_name, $file_type)
    {
        $this->users = [];
        if ($file_type == 'text/x-csv' || $file_type == 'text/csv' || $file_type == 'application/vnd.ms-excel' ||
            $file_type == 'application/octet-stream' || $file_type == 'application/force-download' ||
            $file_type = 'text/comma-separated-values')
        {
            $this->users = Import::csv_to_array($file_name);
        }
        elseif ($file_type == 'text/xml')
        {
            $parser = xml_parser_create();
            xml_set_element_handler($parser, [get_class(), 'element_start'], [get_class(), 'element_end']);
            xml_set_character_data_handler($parser, [get_class(), 'character_data']);
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
            xml_parse($parser, utf8_decode(file_get_contents($file_name)));
            xml_parser_free($parser);
        }
        else
        {
            throw new UserException(
                Translation::getInstance()->getTranslation(
                    'InvalidImportFormat', ['FILE_TYPE' => $file_type], StringUtilities::LIBRARIES
                )
            );
        }

        return $this->users;
    }

    /**
     * Sends an email to the updated/new user
     */
    public function send_email($user, $unencrypted_password)
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $options = [];
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $unencrypted_password;
        $options['site_name'] = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
        $options['site_url'] = $this->getWebPathBuilder()->getBasePath();
        $options['admin_firstname'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_firstname']
        );
        $options['admin_surname'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_surname']
        );
        $options['admin_telephone'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_telephone']
        );
        $options['admin_email'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_email']
        );

        $subject = Translation::get('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = $configurationConsulter->getSetting([Manager::CONTEXT, 'email_template']);
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail(
            $subject, $body, $user->get_email(), true, [], [],
            $options['admin_firstname'] . ' ' . $options['admin_surname'], $options['admin_email']
        );

        $mailer = $this->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception $ex)
        {
        }
    }

    public function validate_data($csvuser)
    {
        $failures = 0;

        if ($csvuser['user_name'])
        {
            $csvuser[User::PROPERTY_USERNAME] = $csvuser['user_name'];
        }

        // 1. Action valid ?
        if ($csvuser['action'])
        {
            $action = strtoupper($csvuser['action']);
            if ($action != 'A' && $action != 'D' && $action != 'U' && $action != 'UA')
            {
                $failures ++;
            }
        }
        else
        {
            $csvuser['action'] = 'A';
            $action = 'A';
        }

        if (empty($csvuser[User::PROPERTY_USERNAME]))
        {
            $failures ++;
        }

        if ($action == 'UA')
        {
            $csvuser['action'] = $this->determineRealAction($csvuser);
            $action = $csvuser['action'];
        }

        // 1. Check if username exists
        if (($action == 'A' && !DataManager::is_username_available($csvuser[User::PROPERTY_USERNAME])) ||
            ($action != 'A' && DataManager::is_username_available($csvuser[User::PROPERTY_USERNAME])))
        {
            $failures ++;
        }
        // 2. Check status
        if ($csvuser[User::PROPERTY_STATUS])
        {
            if ($csvuser[User::PROPERTY_STATUS] != 5 && $csvuser[User::PROPERTY_STATUS] != 1)
            {
                $failures ++;
            }
        }
        else
        {
            $csvuser[User::PROPERTY_STATUS] = 5;
        }

        $email = $csvuser[User::PROPERTY_EMAIL];

        if ($csvuser['phone_number'])
        {
            $csvuser[User::PROPERTY_PHONE] = $csvuser['phone_number'];
        }

        if (!isset($csvuser[User::PROPERTY_ACTIVE]))
        {
            $csvuser[User::PROPERTY_ACTIVE] = 1;
        }

        if (!$csvuser[User::PROPERTY_ACTIVATION_DATE])
        {
            $csvuser[User::PROPERTY_ACTIVATION_DATE] = 0;
        }

        if (!$csvuser[User::PROPERTY_EXPIRATION_DATE])
        {
            $csvuser[User::PROPERTY_EXPIRATION_DATE] = 0;
        }

        if (!$csvuser[User::PROPERTY_AUTH_SOURCE])
        {
            $csvuser[User::PROPERTY_AUTH_SOURCE] = 'Platform';
        }

        $configurationConsulter = $this->getConfigurationConsulter();

        if (!$csvuser['language'])
        {
            $csvuser['language'] = $configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'platform_language']
            );
        }

        if ($action == 'C' && $configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']) &&
            (!$email || $email == ''))
        {
            $failures ++;
        }
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return $csvuser;
        }
    }
}
