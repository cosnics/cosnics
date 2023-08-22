<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BreadcrumbTrail
{

    private static ?BreadcrumbTrail $instance = null;

    /**
     * @var \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    private array $breadcrumbs;

    /**
     * @var string
     */
    private string $containerMode;

    /**
     * @throws \Exception
     */
    public function __construct(bool $includeMainIndex = true, string $containerMode = 'container-fluid')
    {
        $this->breadcrumbs = [];
        $this->containerMode = $containerMode;

        if ($includeMainIndex)
        {
            $classnameUtilities = new ClassnameUtilities(new StringUtilities());
            $webPathBuilder = new WebPathBuilder($classnameUtilities, ChamiloRequest::createFromGlobals());
            $systemPathBuilder = new SystemPathBuilder($classnameUtilities);

            /**
             * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
             */
            $configurationConsulter = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                ConfigurationConsulter::class
            );

            $fileConfigurationLocator = new FileConfigurationLocator($systemPathBuilder);

            // TODO: Can this be fixed more elegantly?
            if ($fileConfigurationLocator->isAvailable())
            {
                $siteName = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
            }
            else
            {
                $siteName = 'Chamilo';
            }

            $this->add(new Breadcrumb($webPathBuilder->getBasePath(true), $siteName, new FontAwesomeGlyph('home')));
        }
    }

    public function add(Breadcrumb $breadcrumb)
    {
        $this->breadcrumbs[] = $breadcrumb;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurationConsulter::class
        );
    }

    /**
     * @return string
     */
    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public static function getInstance(): BreadcrumbTrail
    {
        if (self::$instance == null)
        {
            self::$instance = new BreadcrumbTrail(true, 'container-fluid');
        }

        return self::$instance;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    public function get_breadcrumbtrail(): array
    {
        return $this->breadcrumbs;
    }

    public function get_first(): Breadcrumb
    {
        return $this->breadcrumbs[0];
    }

    public function get_last(): Breadcrumb
    {
        $breadcrumbtrail = $this->breadcrumbs;
        $last_key = count($breadcrumbtrail) - 1;

        return $breadcrumbtrail[$last_key];
    }

    public function merge(BreadcrumbTrail $trail)
    {
        $this->breadcrumbs = array_merge($this->breadcrumbs, $trail->get_breadcrumbtrail());
    }

    public function remove(int $breadcrumbIndex)
    {
        if ($breadcrumbIndex < 0)
        {
            $breadcrumbIndex = count($this->breadcrumbs) + $breadcrumbIndex;
        }

        unset($this->breadcrumbs[$breadcrumbIndex]);
        $this->breadcrumbs = array_values($this->breadcrumbs);
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     */
    public function set(array $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     */
    public function setBreadcrumbs(array $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function setContainerMode(string $containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     *
     * @deprecated Use BreadcrumbTrail::set() now
     */
    public function set_breadcrumbtrail(array $breadcrumbs)
    {
        $this->set($breadcrumbs);
    }

    public function size(): int
    {
        return count($this->breadcrumbs);
    }

    public function truncate(bool $keepMainIndex = false)
    {
        $this->breadcrumbs = [];

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);

        if ($keepMainIndex)
        {
            $this->add(
                new Breadcrumb(
                    $webPathBuilder->getBasePath() . 'index.php',
                    $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'site_name'])
                )
            );
        }
    }
}
