<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Infrastructure\Service\MailNotificationHandler;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Menu;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Translation\Translation;

abstract class ItemComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $portfolio = $this->get_parent()->get_root_content_object();

        if (!$portfolio)
        {
            return $this->display_error_page(Translation::get('NoObjectSelected'));
        }

        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());

        foreach (
            $this->get_root_content_object()->get_complex_content_object_path()->get_parents_by_id(
                $this->get_current_step(), true, true
            ) as $node_parent
        )
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_STEP] = $node_parent->get_id();

            $this->getBreadcrumbTrail()->add(
                new Breadcrumb($this->get_url($parameters), $node_parent->get_content_object()->get_title())
            );
        }

        return $this->build();
    }

    abstract public function build();

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    /**
     * Returns an array of notification handlers
     *
     * @return \Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationHandlerInterface[]
     */
    public function get_notification_handlers()
    {

        return [
            new MailNotificationHandler(
                $this->getActiveMailer(), $this->get_root_content_object()->get_owner(),
                $this->get_current_node()->get_content_object(), $this->get_url()
            )
        ];
    }

    public function render_footer(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3">';

        if ($this->get_parent() instanceof PortfolioComplexRights &&
            $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $virtual_user = $this->get_parent()->get_portfolio_virtual_user();

            if ($virtual_user instanceof User)
            {
                $revert_url = $this->get_url([self::PARAM_ACTION => self::ACTION_USER]);
                $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

                $html[] = '<div class="alert alert-warning">';
                $html[] = Translation::get(
                    'ViewingPortfolioAsUser',
                    ['USER' => $virtual_user->get_fullname(), 'URL' => $revert_url, 'GLYPH' => $glyph->render()]
                );
                $html[] = '</div>';
            }
        }
        $userPictureProvider = $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
        $userPicture = $userPictureProvider->getUserPictureAsBase64String(
            $this->get_root_content_object()->get_owner(), $this->getUser()
        );

        // User photo
        $html[] = '<div class="portfolio-photo-container">';
        $html[] = '<img src="' . $userPicture . '" class="portfolio-photo img-thumbnail" />';
        $html[] = '</div>';

        // Tree menu
        $portfolioMenu = new Menu(
            $this, $this->get_root_content_object()->get_complex_content_object_path(),
            $this->get_parent()->get_portfolio_tree_menu_url(), 'portfolio-menu'
        );

        $html[] = '<div class="clearfix"></div>';
        $html[] = '<div class="portfolio-tree-menu">';
        $html[] = $portfolioMenu->render();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-sm-8 col-md-9">';

        return implode(PHP_EOL, $html);
    }
}