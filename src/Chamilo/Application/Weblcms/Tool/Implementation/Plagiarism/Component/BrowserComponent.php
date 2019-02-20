<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Table\PlagiarismResultTable;
use Chamilo\Application\Plagiarism\Table\PlagiarismResultTableParameters;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager
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


}