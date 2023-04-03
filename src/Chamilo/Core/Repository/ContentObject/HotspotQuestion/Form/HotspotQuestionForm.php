<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Form;

use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestion;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestionAnswer;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.content_object.hotspot_question
 */

/**
 * This class represents a form to create or update hotspot questions
 */
class HotspotQuestionForm extends ContentObjectForm
{

    private $colours = array(
        '#ff0000',
        '#f2ef00',
        '#00ff00',
        '#00ffff',
        '#0000ff',
        '#ff00ff',
        '#0080ff',
        '#ff0080',
        '#00ff80',
        '#ff8000',
        '#8000ff');

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                     'Plugin/jquery.draw.js'));
        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                     'HotspotQuestionForm.js'));

        $this->add_warning_message(
            'hotspot_javascript',
            Translation::get('HotspotJavascriptWarning'),
            Translation::get('HotspotJavascriptRequired'),
            true);

        $this->addElement('html', '<div id="hotspot_options" style="display: none;">');
        $this->addElement('category', Translation::get('Hotspots'));
        $this->add_options();
        $this->addElement('category');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="hotspot_select">');
        $this->addElement('category', Translation::get('Image'));

        $html = array();
        $html[] = '<div id="hotspot_marking" style="display: none;"><div class="colour_box_label">' . Translation::get(
            'CurrentlyMarking') . '</div><div class="colour_box"></div></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clear"></div>';
        $this->addElement('html', implode(PHP_EOL, $html));

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => 'Chamilo\Core\Repository\Ajax',
                Application::PARAM_ACTION => 'XmlImageFeed'));

        $locale = array();
        $locale['Display'] = Translation::get('AddAttachments');
        $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);

        $image_selecter_options = array();
        $image_selecter_options['rescale_image'] = true;
        $image_selecter_options['allow_change'] = false;

        $this->addElement(
            'image_selecter',
            'image',
            Translation::get('SelectImage'),
            $redirect->getUrl(),
            $locale,
            array(),
            $image_selecter_options);
        $this->addElement('category');
        $this->addElement('html', '</div>');

        $this->set_session_answers();
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);

        $this->addElement('category', Translation::get('Properties'));
        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                     'Plugin/jquery.draw.js'));
        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                     'HotspotQuestionForm.js'));
        $this->add_options();

        $html = array();
        $html[] = '<div id="hotspot_marking"><div class="colour_box_label">' . Translation::get('CurrentlyMarking') .
             '</div><div class="colour_box"></div></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clear"></div>';
        $this->addElement('html', implode(PHP_EOL, $html));

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => 'Chamilo\Core\Repository\Ajax',
                Application::PARAM_ACTION => 'XmlImageFeed'));

        $locale = array();
        $locale['Display'] = Translation::get('AddAttachments');
        $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);

        $image_selecter_options = array();
        $image_selecter_options['rescale_image'] = true;
        $image_selecter_options['allow_change'] = false;

        $this->addElement(
            'image_selecter',
            'image',
            Translation::get('SelectImage'),
            $redirect->getUrl(),
            $locale,
            array(),
            $image_selecter_options);

        $this->addElement('category');
        $this->set_session_answers();
    }

    public function setDefaults($defaults = array(), $filter = null)
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            if ($object->get_number_of_answers() != 0)
            {
                $answers = $object->get_answers();
                foreach ($answers as $i => $answer)
                {
                    $defaults['answer'][$i] = $answer->get_answer();
                    $defaults['comment'][$i] = $answer->get_comment();
                    $defaults['coordinates'][$i] = $answer->get_hotspot_coordinates();
                    $defaults['option_weight'][$i] = $answer->get_weight();
                }

                for ($i = count($answers); $i < $_SESSION['mc_number_of_options']; $i ++)
                {
                    $defaults['option_weight'][$i] = 1;
                }

                $defaults['image'] = $object->get_image();
                $this->set_session_answers($defaults);
            }
            else
            {
                $number_of_options = intval($_SESSION['mc_number_of_options']);

                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults['option_weight'][$option_number] = 1;
                }
            }
        }
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $values = $this->exportValues();

        if ($values['image'] == '')
        {
            return false;
        }

        $object = new HotspotQuestion();
        $object->set_image($values['image']);
        $this->set_content_object($object);
        $this->add_options_to_object();
        $success = parent::create_content_object();

        if ($success)
        {
            $object->attach_content_object($values['image']);
        }

        return $object;
    }

    public function update_content_object()
    {
        $this->add_options_to_object();
        unset($_SESSION['web_path']);
        unset($_SESSION['hotspot_path']);
        return parent::update_content_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $object->set_answers('');
        $values = $this->exportValues();
        $answers = $values['answer'];
        $comments = $values['comment'];
        $coordinates = $values['coordinates'];
        $weights = $values['option_weight'];
        for ($i = 0; $i < $_SESSION['mc_number_of_options']; $i ++)
        {
            if (! in_array($i, $_SESSION['mc_skip_options']))
            {
                $answer = new HotspotQuestionAnswer($answers[$i], $comments[$i], $weights[$i], $coordinates[$i]);
                $object->add_answer($answer);
            }
        }
    }

    public function set_session_answers($defaults = array())
    {
        if (count($defaults) == 0)
        {
            $answers = $_POST['answer'];
            $weights = $_POST['option_weight'];
            $coords = $_POST['coordinates'];

            $_SESSION['answers'] = $answers;
            $_SESSION['option_weight'] = $weights;
            $_SESSION['coordinates'] = $coords;
        }
        else
        {
            $_SESSION['answers'] = $defaults['answer'];
            $_SESSION['weights'] = $defaults['weight'];
            $_SESSION['coordinates'] = $defaults['coordinates'];
        }
    }

    public function add_image()
    {
        $object = $this->get_content_object();

        $this->addElement('category', Translation::get('HotspotImage'));

        $html = array();
        $html[] = '<div id="hotspot_marking"><div class="colour_box_label">' . Translation::get('CurrentlyMarking') .
             '</div><div class="colour_box"></div></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clear"></div>';

        if ($object->get_image())
        {
            $image_id = $object->get_image();
            $image_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $image_id);

            $dimensions = getimagesize($image_object->get_full_path());

            $scaledDimensions = Utilities::scaleDimensions(
                600,
                450,
                array('width' => $dimensions[0], 'height' => $dimensions[1]));

            $html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: ' .
                 $scaledDimensions['thumbnailWidth'] . 'px; height: ' . $scaledDimensions['thumbnailHeight'] .
                 'px; background-size: ' . $scaledDimensions['thumbnailWidth'] . 'px ' .
                 $scaledDimensions['thumbnailHeight'] . 'px;background-image: url(' . \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                    $image_object->get_id(),
                    $image_object->calculate_security_code()) . ')"></div></div>';
        }
        else
        {
            $html[] = '<div id="hotspot_container"><div id="hotspot_image"></div></div>';
        }

        // $html[] = '<div class="clear"></div>';
        // $html[] = '<button id="change_image" class="negative delete">' . htmlentities(Translation ::
        // get('SelectAnotherImage')) . '</button>';

        $this->addElement('html', implode(PHP_EOL, $html));
        $this->addElement('category');
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    private function add_options()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        if (! isset($_SESSION['mc_number_of_options']) || $_SESSION['mc_number_of_options'] < 1)
        {
            $_SESSION['mc_number_of_options'] = 1;
        }
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            /*
             * $indexes = array_keys($_POST['remove']); if (!in_array($indexes[0],$_SESSION['mc_skip_options']))
             * $_SESSION['mc_skip_options'][] = $indexes[0];
             */
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] - 1;

            // $this->move_answer_arrays($indexes[0]);
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_answers() != 0)
        {
            $_SESSION['mc_number_of_options'] = $object->get_number_of_answers();

            // $_SESSION['mc_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['mc_number_of_options']);

        if (isset($_SESSION['file']))
        {
            $this->addElement('html', '<div class="content_object">');
            $this->addElement('html', '</div>');
        }

        $this->addElement(
            'hidden',
            'mc_number_of_options',
            $_SESSION['mc_number_of_options'],
            array('id' => 'mc_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button',
            'add[]',
            Translation::get('AddHotspotOption'),
            array('class' => 'add_option'),
            null,
            'plus');
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;

        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation::get('HotspotDescription') . '</th>';
        $table_header[] = '<th>' . Translation::get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation::get('Score') . '</th>';
        $table_header[] = '<th></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));

        $colours = $this->colours;

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->createElement(
                    'static',
                    null,
                    null,
                    '<div class="colour_box" style="background-color: ' . $colours[$option_number] . ';"></div>');
                // $group[] = $this->createElement('hidden', 'type[' . $option_number . ']', '');
                $group[] = $this->createElement('hidden', 'coordinates[' . $option_number . ']', '');
                $group[] = $this->create_html_editor(
                    'answer[' . $option_number . ']',
                    Translation::get('Answer'),
                    $html_editor_options);
                $group[] = $this->create_html_editor(
                    'comment[' . $option_number . ']',
                    Translation::get('Comment'),
                    $html_editor_options);
                $group[] = $this->createElement(
                    'text',
                    'option_weight[' . $option_number . ']',
                    Translation::get('Weight'),
                    'size="2"  class="input_numeric"');

                $hotspot_actions = array();
                $hotspot_actions[] = $this->createElement(
                    'image',
                    'edit[' . $option_number . ']',
                    Theme::getInstance()->getCommonImagePath('Action/Edit'),
                    array('class' => 'edit_option', 'id' => 'edit_' . $option_number));
                $hotspot_actions[] = $this->createElement(
                    'image',
                    'reset[' . $option_number . ']',
                    Theme::getInstance()->getCommonImagePath('Action/Reset'),
                    array('class' => 'reset_option', 'id' => 'reset_' . $option_number));

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $hotspot_actions[] = $this->createElement(
                        'image',
                        'remove[' . $option_number . ']',
                        Theme::getInstance()->getCommonImagePath('Action/Delete'),
                        array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $hotspot_actions[] = $this->createElement(
                        'static',
                        null,
                        null,
                        '<img class="remove_option" src="' . Theme::getInstance()->getCommonImagePath('Action/DeleteNa') .
                             '" />');
                }
                $group[] = $this->createElement(
                    'static',
                    null,
                    null,
                    $this->createElement('group', null, null, $hotspot_actions, '&nbsp;&nbsp;', false)->toHtml());

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $this->addGroupRule(
                    'option_' . $option_number,
                    array(
                        "option_weight[$option_number]" => array(
                            array(
                                Translation::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
                                'numeric'))));

                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') .
                         '">{element}</tr>',
                        'option_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);
            }
        }

        $this->setDefaults();

        $_SESSION['mc_num_options'] = $number_of_options;
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>',
            'question_buttons');
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>',
            'question_buttons');
    }

    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }
}
