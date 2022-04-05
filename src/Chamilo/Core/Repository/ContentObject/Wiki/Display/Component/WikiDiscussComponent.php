<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\WikiPage;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\WikiPageFeedback;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use MediawikiParser;
use MediawikiParserContext;

/**
 *
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the discuss page. Here a user can add feedback to a wiki_page. Author: Stefan Billiet Author: Nick De Feyter
 */
require_once Path::getInstance()->getPluginPath() . 'wiki/mediawiki_parser.class.php';
require_once Path::getInstance()->getPluginPath() . 'wiki/mediawiki_parser_context.class.php';
class WikiDiscussComponent extends Manager implements DelegateComponent, FeedbackSupport
{
    /*
     * private $wiki_page_id; private $complex_id; private $feedback_id; private $links;
     */
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    /**
     * The Wiki Page Wrapper
     *
     * @var ComplexWikiPage
     */
    protected $complexWikiPage;

    /**
     * The Wiki Page
     *
     * @var WikiPage
     */
    protected $wikiPage;

    public function run()
    {
        $this->set_parameter(
            self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID,
            $this->get_selected_complex_content_object_item_id());

        $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $this->complexWikiPage = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(),
            $complex_wiki_page_id);
        $this->wikiPage = $this->complexWikiPage->get_ref_object();

        $html = array();

        $html[] = $this->render_header();

        $html[] = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Feedback\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_header($pageTitle = null)
    {
        $html = array();

        $html[] = parent::render_header($this->complexWikiPage);

        $parser = new MediawikiParser(
            new MediawikiParserContext(
                $this->get_root_content_object(),
                $this->wikiPage->get_title(),
                $this->wikiPage->get_description(),
                $this->get_parameters()));

        $html[] = '<div class="wiki-pane-content-title">' . Translation::get('Discuss') . ' ' .
             $this->wikiPage->get_title() . '</div>';
        $html[] = '<div class="wiki-pane-content-subtitle">' . Translation::get(
            'From',
            null,
            Utilities::COMMON_LIBRARIES) . ' ' . $this->get_root_content_object()->get_title() . '</div>';
        $html[] = '<div class="wiki-pane-content-discuss">';
        $html[] = $parser->parse($this->wikiPage->get_description());
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '<div class="wiki-pane-content-feedback">';

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        return \Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataManager::retrieves(
            WikiPageFeedback::class_name(),
            new DataClassRetrievesParameters($this->getWikiPageFeedbackCondition(), $count, $offset));
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return \Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataManager::count(
            WikiPageFeedback::class_name(),
            new DataClassCountParameters($this->getWikiPageFeedbackCondition()));
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedback_id)
    {
        return \Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataManager::retrieve_by_id(
            WikiPageFeedback::class_name(),
            $feedback_id);
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        $wikiPageFeedback = new WikiPageFeedback();
        $wikiPageFeedback->setWikiPageId($this->wikiPage->getId());

        return $wikiPageFeedback;
    }

    /**
     * Returns the condition needed to retrieve / count feedback objects for the current wiki page
     *
     * @return EqualityCondition
     */
    protected function getWikiPageFeedbackCondition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(WikiPageFeedback::class_name(), WikiPageFeedback::PROPERTY_WIKI_PAGE_ID),
            new StaticConditionVariable($this->wikiPage->getId()));
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return true;
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return true;
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return true;
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return true;
    }
}
