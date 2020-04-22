<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Form;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/*
 * @author Mattias De Pauw - Hogeschool Gent @author Maarten Volckaert - Hogeschool Gent
 */

class ForumPostForm extends FormValidator
{
    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    const TYPE_CREATE = 1;

    const TYPE_EDIT = 3;

    const TYPE_QUOTE = 2;

    /**
     * Determine which kind of form it is.
     *
     * @var int
     */
    private $form_type;

    /**
     * Contains a Forum Post Object.
     *
     * @var ForumPost
     */
    private $forumpost;

    private $reply_on_id;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */
    public function __construct($form_type, $action, $forumpost, $id_reply_on)
    {
        parent::__construct('forum_post_form', self::FORM_METHOD_POST, $action);
        $this->form_type = $form_type;
        $this->forumpost = $forumpost;
        $this->reply_on_id = $id_reply_on;
        $this->build_basic_form();
        $this->add_footer();
        $this->setdefaults();
    }

    /**
     * **************************************************************************************************************
     * Form functions *
     * **************************************************************************************************************
     */

    /**
     * Adds a footer to the form, this function generates the attachment part.
     */
    protected function add_footer()
    {
        $calculator = new Calculator(
            DataManager::retrieve_by_id(
                User::class_name(), Session::get_user_id()
            )
        );

        $uploadUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(),
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
            )
        );

        $dropZoneParameters = array(
            'name' => 'attachments_importer',
            'maxFilesize' => $calculator->getMaximumUploadSize(),
            'uploadUrl' => $uploadUrl->getUrl(),
            'successCallbackFunction' => 'chamilo.core.repository.importAttachment.processUploadedFile',
            'sendingCallbackFunction' => 'chamilo.core.repository.importAttachment.prepareRequest',
            'removedfileCallbackFunction' => 'chamilo.core.repository.importAttachment.deleteUploadedFile'
        );

        $this->addElement(
            'category', '<a href="#">' . Translation::get('Attachments') . '</a>'
        );

        $this->addFileDropzone('attachments_importer', $dropZoneParameters, true);

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'Plugin/jquery.file.upload.import.js'
        )
        );

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'content_objects', Translation::get('ContentObjects'), 'Chamilo\Core\Repository\Ajax',
                'AttachmentContentObjectsFeed'
            )
        );

        $this->addElement(
            'advanced_element_finder', 'attachments', Translation::get('SelectAttachment'), $types
        );

        if (count($this->additional_elements) > 0)
        {
            $count = 0;
            foreach ($this->additional_elements as $element)
            {
                if ($element->getType() != 'hidden')
                {
                    $count ++;
                }
            }
            if ($count > 0)
            {
                $this->addElement('category', Translation::get('AdditionalProperties'));
                foreach ($this->additional_elements as $element)
                {
                    $this->addElement($element);
                }
            }
        }

        // Defining the button:
        $buttons = array();

        switch ($this->form_type)
        {
            case self::TYPE_EDIT :
                $buttons[] = $this->createElement(
                    'style_submit_button', 'submit_button',
                    Translation::get('Update', null, Utilities::COMMON_LIBRARIES), null, null,
                    new FontAwesomeGlyph('arrow-right')
                );
                break;
            case self::TYPE_QUOTE :
                $buttons[] = $this->createElement(
                    'style_submit_button', 'submit_button',
                    Translation::get('Quote', null, Utilities::COMMON_LIBRARIES), null, null,
                    new FontAwesomeGlyph('envelope')
                );
                break;
            default :
                $buttons[] = $this->createElement(
                    'style_submit_button', 'submit_button',
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES)
                );
                break;
        }

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectFormUpload.js'
        )
        );
    }

    /**
     * **************************************************************************************************************
     * Defaults *
     * **************************************************************************************************************
     */

    /**
     * Constructs a basic form.
     */
    public function build_basic_form()
    {
        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));

        // title field
        $this->add_textfield(ForumPost::PROPERTY_TITLE, Translation::get('Title'), false, array("size" => "50"));

        // content HTML editor
        $this->add_html_editor(ForumPost::PROPERTY_CONTENT, Translation::get('Content'), true);
    }

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $forump = $this->forumpost;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $defaults[ForumPost::PROPERTY_TITLE] = $forump->get_title();
            $defaults[ForumPost::PROPERTY_CONTENT] = $forump->get_content();
        }

        if ($this->form_type == self::TYPE_QUOTE)
        {
            $defaults[ForumPost::PROPERTY_CONTENT] = $forump->get_content();
        }

        if ($this->form_type == self::TYPE_CREATE || $this->form_type == self::TYPE_QUOTE)
        {
            if (substr($forump->get_title(), 0, 3) == 'RE:')
            {
                $defaults[ForumPost::PROPERTY_TITLE] = $forump->get_title();
            }
            else
            {
                $defaults[ForumPost::PROPERTY_TITLE] = 'RE: ' . $forump->get_title();
            }
        }

        parent::setDefaults($defaults);
    }
}
