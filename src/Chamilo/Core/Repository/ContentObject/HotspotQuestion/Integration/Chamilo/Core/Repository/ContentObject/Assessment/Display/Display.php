<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: hotspot_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
class Display extends QuestionDisplay
{
    // private $colours = array('#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019',
    // '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429',
    // '#f6d7c5', '#7a2893');
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
        '#8000ff'
    );

    public function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        if ($clo_question->get_random())
        {
            $answers = $this->shuffle_with_keys($question->get_answers());
        }
        else
        {
            $answers = $question->get_answers();
        }

        $renderer = $this->get_renderer();

        $question_id = $clo_question->get_id();

        $formvalidator->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                'Plugin/jquery.draw.js'
            )
        );
        $formvalidator->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                'HotspotQuestionDisplay.js'
            )
        );

        $image_html = array();
        $image_object = $question->get_image_object();
        $dimensions = getimagesize($image_object->get_full_path());

        $scaledDimensions = Utilities::scaleDimensions(
            600,
            450,
            array('width' => $dimensions[0], 'height' => $dimensions[1])
        );

        $image_html[] = '<div class="description_hotspot">';
        $image_html[] = '<div id="hotspot_container_' . $question_id .
            '" class="hotspot_container"><div id="hotspot_image_' . $question_id .
            '" class="hotspot_image" style="width: ' . $scaledDimensions['thumbnailWidth'] . 'px; height: ' .
            $scaledDimensions['thumbnailHeight'] . 'px; background-size: ' . $scaledDimensions['thumbnailWidth'] .
            'px ' .
            $scaledDimensions['thumbnailHeight'] . 'px;background-image: url(' .
            \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                $image_object->get_id(),
                $image_object->calculate_security_code()
            ) . ')"></div></div>';
        $image_html[] = '<div class="clear"></div>';
        $image_html[] = '<div id="hotspot_marking_' . $question_id . '" class="hotspot_marking">';
        $image_html[] = '<div class="colour_box_label">' . Translation::get('CurrentlyMarking') . '</div>';
        $image_html[] = '<div class="colour_box"></div>';
        $image_html[] = '<div class="clear"></div>';
        $image_html[] = '</div>';
        $image_html[] = '<div class="clear"></div>';
        $image_html[] = '<div class="alert alert-info hotspot-question-info">' .
            Translation::getInstance()->getTranslation('HotspotQuestionExecuteInformation') . '</div>';

        $image_html[] = '</div>';
        $formvalidator->addElement('html', implode(PHP_EOL, $image_html));

        $table_header = array();
        $table_header[] =
            '<table class="table table-striped table-bordered table-hover table-data take_assessment hotspot_question_options">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

        foreach ($answers as $i => $answer)
        {
            $answer_name = $question_id . '_' . $i;

            $group = array();
            $group[] = $formvalidator->createElement(
                'static',
                null,
                null,
                '<div class="colour_box" id="colour_' . $answer_name . '" style="background-color: ' .
                $this->colours[$i] .
                ';"></div>'
            );

            $object_renderer = new ContentObjectResourceRenderer(
                $this->get_formvalidator()->get_assessment_viewer(),
                $answer->get_answer()
            );

            $group[] = $formvalidator->createElement('static', null, null, $object_renderer->run());
            $group[] = $formvalidator->createElement(
                'static',
                null,
                null,
                '<img id="reset_' . $answer_name . '" class="reset_option" type="image" src="' .
                Theme::getInstance()->getCommonImagePath('Action/Reset') . '" />'
            );
            $group[] = $formvalidator->createElement('hidden', $answer_name, '', 'class="hotspot_coordinates"');

            // $formvalidator->addGroup($group, 'option_' . $i, null, '', false);
            $formvalidator->addGroup($group, 'option_' . $question_id . '_' . $i, null, '', false);

            // $renderer->setElementTemplate('<tr id="' . $answer_name . '" class="' . ($i % 2 == 0 ? 'row_even' :
            // 'row_odd') . '">{element}</tr>', 'option_' . $i);
            // $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
            $renderer->setElementTemplate(
                '<tr id="' . $answer_name . '" class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'option_' . $question_id . '_' . $i
            );
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $question_id . '_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));

        // $this->add_scripts_element($clo_question->get_id(), $formvalidator);
        // //$formvalidator->addElement('html', '<br/>');
        // $answers = $question->get_answers();
        // foreach ($answers as $i => $answer)
        // {
        // $formvalidator->addElement('hidden', $clo_question->get_id().'_'.$i, '', array('id' =>
        // $clo_question->get_id().'_'.$i));
        // }
    }

    public function get_instruction()
    {
        return Translation::get('MarkHotspots');
    }
}
