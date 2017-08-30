<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Utilities function for javascript manipulation
 *
 * @package libraries
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ResourceUtilities
{
    const PARAM_TYPE = 'type';
    const PARAM_THEME = 'theme';
    const PARAM_CONTEXT = 'context';
    const DEFAULT_CHARSET = 'utf-8';

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    private $configurablePathBuilder;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilties;

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @param string $context
     * @param Theme $themeUtilities
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param ClassnameUtilities $classnameUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct($context = __NAMESPACE__, Theme $themeUtilities, PathBuilder $pathBuilder,
        ConfigurablePathBuilder $configurablePathBuilder, ClassnameUtilities $classnameUtilities,
        \Chamilo\Libraries\Platform\ChamiloRequest $request)
    {
        $this->context = $context;
        $this->themeUtilities = $themeUtilities;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->pathBuilder = $pathBuilder;
        $this->classnameUtilities = $classnameUtilities;
        $this->request = $request;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities()
    {
        return $this->themeUtilities;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities($themeUtilities)
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder()
    {
        return $this->configurablePathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function setConfigurablePathBuilder($configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder($pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilties()
    {
        return $this->classnameUtilties;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilties
     */
    public function setClassnameUtilties($classnameUtilties)
    {
        $this->classnameUtilties = $classnameUtilties;
    }

    static public function launch(\Chamilo\Libraries\Platform\ChamiloRequest $request)
    {
        $type = Request::get(self::PARAM_TYPE, null);

        if ($type)
        {
            $classname = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
                 'Utilities';

            if (class_exists($classname))
            {
                $theme = Request::get(self::PARAM_THEME);
                $context = Request::get(self::PARAM_CONTEXT, __NAMESPACE__);

                $stringUtilities = new StringUtilities();
                $classnameUtilities = new ClassnameUtilities($stringUtilities);
                $pathBuilder = new PathBuilder($classnameUtilities);

                $fileConfigurationConsulter = new ConfigurationConsulter(
                    new FileConfigurationLoader(new FileConfigurationLocator($pathBuilder)));

                $configurablePathBuilder = new ConfigurablePathBuilder(
                    $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'storage')));

                $themeUtilities = new Theme($theme, $stringUtilities, $classnameUtilities, $pathBuilder);

                $utilities = new $classname(
                    $context,
                    $themeUtilities,
                    $pathBuilder,
                    $configurablePathBuilder,
                    $classnameUtilities,
                    $request);

                $utilities->run();
            }
        }
    }
}
