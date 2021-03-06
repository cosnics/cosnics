<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Core\User\Renderer\LoginFormRenderer;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotAuthenticatedResponse extends Response
{
    use DependencyInjectionContainerTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $page = Page::getInstance();

        $html = array();
        $html[] = $page->getHeader()->toHtml();
        $html[] = $this->renderPanel();
        $html[] = $page->getFooter()->toHtml();

        parent::__construct('', implode(PHP_EOL, $html));
    }

    /**
     * Renders the panel with the not authenticated message and the login form
     *
     * @return string
     */
    public function renderPanel()
    {
        $html = array();

        $html[] = '<div class="panel panel-danger panel-not-authenticated">';
        $html[] = '<div class="panel-heading">';
        $html[] = Translation::getInstance()->getTranslation('NotAuthenticated', array(), Utilities::COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $this->displayLoginForm();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the login form
     *
     * @return string
     */
    public function displayLoginForm()
    {
        $this->initializeContainer();
        return $this->getService(LoginFormRenderer::class)->renderLoginForm();
    }
}
