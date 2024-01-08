<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Menu;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Infrastructure\Service\MailNotificationHandler;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Translation\Translation;

abstract class ItemComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $portfolio = $this->get_parent()->get_root_content_object();
        
        $trail = BreadcrumbTrail::getInstance();
        
        if (! $portfolio)
        {
            return $this->display_error_page(Translation::get('NoObjectSelected'));
        }
        
        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());
        
        foreach ($this->get_root_content_object()->get_complex_content_object_path()->get_parents_by_id(
            $this->get_current_step(), 
            true, 
            true) as $node_parent)
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_STEP] = $node_parent->get_id();
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb($this->get_url($parameters), $node_parent->get_content_object()->get_title()));
        }
        
        return $this->build();
    }

    abstract public function build();

    /**
     *
     * @see \libraries\SubManager::render_header()
     */
    public function render_header($pageTitle = null)
    {
        $html = array();
        
        $html[] = parent::render_header();
        $html[] = '<div class="col col-sm-4 col-md-3">';
        
        if ($this->get_parent() instanceof PortfolioComplexRights &&
             $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $virtual_user = $this->get_parent()->get_portfolio_virtual_user();
            
            if ($virtual_user instanceof \Chamilo\Core\User\Storage\DataClass\User)
            {
                $revert_url = $this->get_url(array(self::PARAM_ACTION => self::ACTION_USER));
                $image_url = Theme::getInstance()->getImagePath(Manager::package(), 'Action/' . self::ACTION_USER);
                
                $html[] = '<div class="alert alert-warning">';
                $html[] = Translation::get(
                    'ViewingPortfolioAsUser', 
                    array('USER' => $virtual_user->get_fullname(), 'URL' => $revert_url, 'IMAGE' => $image_url));
                $html[] = '</div>';
            }
        }
        
        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE, 
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $this->get_root_content_object()->get_owner()->get_id()));
        
        // User photo
        // $html[] = '<div class="panel panel-default panel-portfolio">';
        // $html[] = '<div class="panel-body">';
        $html[] = '<div class="portfolio-photo-container">';
        $html[] = '<img src="' . $profilePhotoUrl->getUrl() . '" class="portfolio-photo img-thumbnail" />';
        $html[] = '</div>';
        // $html[] = '</div>';
        // $html[] = '</div>';
        
        // Tree menu
        $portfolioMenu = new Menu(
            $this, 
            $this->get_root_content_object()->get_complex_content_object_path(), 
            $this->get_parent()->get_portfolio_tree_menu_url(), 
            'portfolio-menu');
        
        $html[] = '<div class="clearfix"></div>';
        $html[] = '<div class="portfolio-tree-menu">';
        $html[] = $portfolioMenu->render();
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="col col-sm-8 col-md-9">';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::render_footer()
     */
    public function render_footer()
    {
        $html = array();
        
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = parent::render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns an array of notification handlers
     * 
     * @return NotificationHandlerInterface[]
     */
    public function get_notification_handlers()
    {
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        
        return array(
            new MailNotificationHandler(
                $mailerFactory->getActiveMailer(), 
                $this->get_root_content_object()->get_owner(), 
                $this->get_current_node()->get_content_object(), 
                $this->get_url()));
    }
}