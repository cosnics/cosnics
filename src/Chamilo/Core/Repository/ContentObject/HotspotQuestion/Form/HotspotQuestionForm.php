<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Form;

use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestion;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestionAnswer;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.content_object.hotspot_question
 */

/**
 * This class represents a form to create or update hotspot questions
 */
class HotspotQuestionForm extends ContentObjectForm
{

    private $colours = [
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
        '#8000ff'
    ];

    protected function addFileUploadSelection()
    {
        $calculator = new Calculator(
            DataManager::retrieve_by_id(
                User::class, (int) $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_IO)
            )
        );

        $uploadUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::CONTEXT,
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
            ]
        );

        $dropZoneParameters = [
            'name' => HotspotQuestion::PROPERTY_IMAGE . '_dropzone',
            'maxFilesize' => $calculator->getMaximumUploadSize(),
            'uploadUrl' => $uploadUrl,
            'maxFiles' => 1,
            'successCallbackFunction' => 'chamilo.core.repository.importImage.processUploadedFile',
            'sendingCallbackFunction' => 'chamilo.core.repository.importImage.prepareRequest',
            'removedfileCallbackFunction' => 'chamilo.core.repository.importImage.deleteUploadedFile'
        ];

        $this->addElement('html', '<div id="hotspot-image-select">');

        $this->addFileDropzone(HotspotQuestion::PROPERTY_IMAGE . '_dropzone', $dropZoneParameters);

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(Manager::CONTEXT) . 'Plugin/jquery.file.upload.import.js'
        )
        );

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'content_objects', Translation::get('ContentObjects'), 'Chamilo\Core\Repository\Ajax',
                'AttachmentImagesFeed'
            )
        );

        $this->addElement(
            'advanced_element_finder', HotspotQuestion::PROPERTY_IMAGE . '_finder', Translation::get('SelectImage'),
            $types
        );

        $this->addElement('html', '</div>');

        $this->addImagePreview();
    }

    protected function addImagePreview()
    {
        $html = [];

        $contentObject = $this->get_content_object();

        if ($contentObject instanceof ContentObject)
        {
            $html[] = '<div class="clearfix" id="hotspot-image-container">';

            $imageObject = $contentObject->get_image_object();

            $dimensions = getimagesize($imageObject->get_full_path());

            $scaledDimensions = ImageManipulation::rescale($dimensions[0], $dimensions[1], 600, 450);

            $styleProperties = [];
            $styleProperties['width'] = $scaledDimensions[ImageManipulation::DIMENSION_WIDTH] . 'px';
            $styleProperties['height'] = $scaledDimensions[ImageManipulation::DIMENSION_HEIGHT] . 'px';
            $styleProperties['background-size'] = $scaledDimensions[ImageManipulation::DIMENSION_WIDTH] . 'px ' .
                $scaledDimensions[ImageManipulation::DIMENSION_HEIGHT] . 'px';
            $styleProperties['background-image'] =
                'url(' . Manager::get_document_downloader_url($imageObject->get_id()) . ')';

            $styleValues = [];

            foreach ($styleProperties as $styleKey => $styleValue)
            {
                $styleValues[] = $styleKey . ': ' . $styleValue;
            }

            $html[] = '<div id="hotspot-selected-image" style="' . implode(';', $styleValues) . '"></div>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="clearfix hidden" id="hotspot-image-container">';
            $html[] = '<div id="hotspot-selected-image" class="clearfix"></div>';
            $html[] = '</div>';
        }

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    private function add_options()
    {
        if (!$this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        if (!isset($_SESSION['mc_number_of_options']) || $_SESSION['mc_number_of_options'] < 1)
        {
            $_SESSION['mc_number_of_options'] = 1;
        }
        if (!isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = [];
        }
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] - 1;
        }
        $object = $this->get_content_object();
        if (!$this->isSubmitted() && $object->get_number_of_answers() != 0)
        {
            $_SESSION['mc_number_of_options'] = $object->get_number_of_answers();
        }
        $number_of_options = intval($_SESSION['mc_number_of_options']);

        $this->addElement(
            'hidden', 'mc_number_of_options', $_SESSION['mc_number_of_options'], ['id' => 'mc_number_of_options']
        );

        $buttons = [];
        $buttons[] = $this->createElement(
            'style_button', 'add[]', Translation::get('AddHotspotOption'), ['class' => 'add_option'], null,
            new FontAwesomeGlyph('plus')
        );
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $html_editor_options = [];
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;

        $table_header = [];
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x3"></th>';
        $table_header[] = '<th>' . Translation::get('HotspotDescription') . '</th>';
        $table_header[] = '<th>' . Translation::get('Feedback') . '</th>';
        $table_header[] = '<th class="cell-stat-x2">' . Translation::get('Score') . '</th>';
        $table_header[] = '<th></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));

        $colours = $this->colours;

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (!in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = [];
                $group[] = $this->createElement(
                    'static', null, null,
                    '<div class="colour_box" style="background-color: ' . $colours[$option_number] . ';"></div>'
                );
                // $group[] = $this->createElement('hidden', 'type[' . $option_number . ']', '');
                $group[] = $this->createElement('hidden', 'coordinates[' . $option_number . ']', '');
                $group[] = $this->create_html_editor(
                    'answer[' . $option_number . ']', Translation::get('Answer'), $html_editor_options
                );
                $group[] = $this->create_html_editor(
                    'comment[' . $option_number . ']', Translation::get('Comment'), $html_editor_options
                );
                $group[] = $this->createElement(
                    'text', 'option_weight[' . $option_number . ']', Translation::get('Weight'),
                    'size="2"  class="input_numeric"'
                );

                $hotspot_actions = [];

                $hotspot_actions[] = $this->createElement(
                    'style_button', 'edit[' . $option_number . ']', null,
                    ['class' => 'edit_option', 'id' => 'edit_' . $option_number], null,
                    new FontAwesomeGlyph('pencil-alt', [], null, 'fas')
                );

                $hotspot_actions[] = $this->createElement(
                    'style_button', 'reset[' . $option_number . ']', null,
                    ['class' => 'reset_option', 'id' => 'reset_' . $option_number], null,
                    new FontAwesomeGlyph('undo', [], null, 'fas')
                );

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $hotspot_actions[] = $this->createElement(
                        'style_button', 'remove[' . $option_number . ']', null,
                        ['class' => 'remove_option', 'id' => 'remove_' . $option_number], null,
                        new FontAwesomeGlyph('times', [], null, 'fas')
                    );
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('times', ['text-muted', 'remove_option']);
                    $hotspot_actions[] = $this->createElement(
                        'static', null, null,
                        '<button class="btn btn-default" disabled=""disabled">' . $glyph->render() . '</button>'
                    );
                }
                $group[] = $this->createElement(
                    'static', null, null,
                    $this->createElement('group', null, null, $hotspot_actions, '&nbsp;&nbsp;', false)->toHtml()
                );

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $this->addGroupRule(
                    'option_' . $option_number, [
                        "option_weight[$option_number]" => [
                            [
                                Translation::get('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
                                'numeric'
                            ]
                        ]
                    ]
                );

                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' .
                    ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $option_number
                );
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
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clearfix"></div></div>', 'question_buttons'
        );
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons'
        );
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
            if (!in_array($i, $_SESSION['mc_skip_options']))
            {
                $answer = new HotspotQuestionAnswer($answers[$i], $comments[$i], $weights[$i], $coordinates[$i]);
                $object->add_answer($answer);
            }
        }
    }

    protected function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form();

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getPluginPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion') .
            'jquery.draw.js'
        )
        );

        $this->add_warning_message(
            'hotspot_javascript', Translation::get('HotspotJavascriptWarning'),
            Translation::get('HotspotJavascriptRequired'), true
        );

        $this->addElement('html', '<div id="hotspot_options" style="display: none;">');
        $this->addElement('category', Translation::get('Hotspots'));
        $this->add_options();
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="hotspot_select">');
        $this->addElement('category', Translation::get('Image'));

        $html = [];
        $html[] = '<div id="hotspot_marking" style="display: none;"><div class="colour_box_label">' . Translation::get(
                'CurrentlyMarking'
            ) . '</div><div class="colour_box"></div></div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clearfix"></div>';
        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addFileUploadSelection();

        $this->addElement('html', '</div>');

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\HotspotQuestion'
            ) . 'HotspotQuestionForm.js'
        )
        );

        $this->set_session_answers();
    }

    protected function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form();

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getPluginPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion') .
            'jquery.draw.js'
        )
        );

        $this->add_warning_message(
            'hotspot_javascript', Translation::get('HotspotJavascriptWarning'),
            Translation::get('HotspotJavascriptRequired'), true
        );

        $this->addElement('html', '<div id="hotspot_options">');
        $this->addElement('category', Translation::get('Hotspots'));
        $this->add_options();
        $this->addElement('html', '</div>');

        $html = [];
        $html[] = '<div id="hotspot_marking"><div class="colour_box_label">' . Translation::get('CurrentlyMarking') .
            '</div><div class="colour_box"></div></div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clearfix"></div>';
        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addImagePreview();

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion') .
            'HotspotQuestionForm.js'
        )
        );

        $this->set_session_answers();
    }

    public function create_content_object()
    {
        $values = $this->exportValues();

        $imageFinderValues = $values['image_finder'];

        if (is_null($imageFinderValues))
        {
            return false;
        }

        $selectedImageIdentifier = $imageFinderValues['content_object'][0];

        $object = new HotspotQuestion();
        $object->set_image($selectedImageIdentifier);
        $this->set_content_object($object);
        $this->add_options_to_object();
        $success = parent::create_content_object();

        if ($success)
        {
            $object->attach_content_object($selectedImageIdentifier);
        }

        return $object;
    }

    public function generateTabs()
    {
        $this->addDefaultTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        if (!$this->isSubmitted())
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

    public function set_session_answers($defaults = [])
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

    public function update_content_object()
    {
        $this->add_options_to_object();
        unset($_SESSION['web_path']);
        unset($_SESSION['hotspot_path']);

        return parent::update_content_object();
    }
}
