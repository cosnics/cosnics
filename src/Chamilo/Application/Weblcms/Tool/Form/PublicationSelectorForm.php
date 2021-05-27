<?php
namespace Chamilo\Application\Weblcms\Tool\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Enhancement form to select publications with JSTree
 *
 * @author Minas Zilyas - Hogeschool Gent
 */
class PublicationSelectorForm
{

    private $publications;

    private $categories;

    private $course_title;

    private $check_parents;

    private $translations;

    public function __construct($publications, $categories, $course_title, $check_parents, $translations)
    {
        $this->publications = $publications;
        $this->categories = $categories;
        $this->course_title = $course_title;
        $this->check_parents = $check_parents;
        $this->translations = $translations;
    }

    public function render()
    {
        $html = [];
        $resourceManager = ResourceManager::getInstance();
        $path = Path::getInstance();
        $translator = Translation::getInstance();

        $context = 'Chamilo\Application\Weblcms\Tool';

        $html[] = $resourceManager->getResourceHtml($path->getPluginPath($context, true) . 'jstree/style.min.css');
        $html[] = $resourceManager->getResourceHtml($path->getPluginPath($context, true) . 'jstree/jstree.min.js');
        $html[] = $resourceManager->getResourceHtml(
            $path->getJavascriptPath($context, true) . 'Resources/Javascript/PublicationSelector.js'
        );

        $html[] = '<div id="dataJSON" style="display: none;">';
        $html[] = '<script id="categoriesJSON" type="application/json">' . json_encode($this->categories) . '</script>';
        $html[] =
            '<script id="publicationsJSON" type="application/json">' . json_encode($this->publications) . '</script>';
        $html[] = '<script id="courseName" type="application/json">' . $this->course_title . '</script>';
        $html[] = '<script id="translations" type="application/json">' . json_encode($this->translations) . '</script>';
        $html[] = '<script id="checkParentsBoolean" type="application/json">' . json_encode($this->check_parents) .
            '</script>';
        $html[] = '</div>';

        $html[] = '<div class="form-row row">';
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label">' .
            $translator->getTranslation('Publications', null, $context) . '</div>';
        $html[] = '<div id="publications_tree" class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw"></div>';
        $html[] = '</div>';

        $html[] = '<div id="checkboxes_action" style="margin-left: 20%; padding-top: 5px;">';
        $html[] = '<a id="selectAll" href="#">' .
            $translator->getTranslation('SelectAll', null, Utilities::COMMON_LIBRARIES) . '</a>';
        $html[] = '<a id="deselectAll" href="#" style="padding-left: 20px;">' .
            $translator->getTranslation('UnselectAll', null, Utilities::COMMON_LIBRARIES) . '</a>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}