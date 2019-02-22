<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Table\PlagiarismResultTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Table\PlagiarismResultTableParameters;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager implements TableSupport
{
    /**
     * @return \Chamilo\Libraries\Format\Response\Response|string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $toolbar = new ButtonToolBar($this->get_url());
        $toolbar->addItem(
            new Button(
                $this->getTranslator()->trans('CheckForPlagiarism', [], Manager::context()),
                new FontAwesomeGlyph('plus'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_CHECK_PLAGIARISM])
            )
        );

        $toolbar->addItem(
            new Button(
                $this->getTranslator()->trans('RefreshRetry', [], Manager::context()),
                new FontAwesomeGlyph('refresh'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_REFRESH])
            )
        );

        $toolbarRenderer = new ButtonToolBarRenderer($toolbar);

        $parameters = new PlagiarismResultTableParameters(
            $this->getContentObjectPlagiarismResultService(), $this->get_course()
        );

        $table = new PlagiarismResultTable($this, $parameters);
        $table->setSearchForm($toolbarRenderer->getSearchForm());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $toolbarRenderer->render();
        $html[] = $table->render();
        $html[] = $this->render_footer();

        return new Response(null, implode(PHP_EOL, $html));
    }

    /**
     * Returns the condition
     *
     * @param string $tableClassname
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($tableClassname)
    {
        return null;
    }
}