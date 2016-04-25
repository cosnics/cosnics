<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
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
    const PARAM_SERVER_TYPE = 'serverType';
    const PARAM_CONTEXT = 'context';
    const DEFAULT_CHARSET = 'utf-8';

    /**
     *
     * @var string
     */
    private $serverType;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var boolean
     */
    private $cachingEnabled;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     *
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilties;

    /**
     *
     * @param string $serverType
     * @param string $context
     * @param Theme $themeUtilities
     */
    public function __construct($serverType = 'production', $context = __NAMESPACE__, Theme $themeUtilities, Path $pathUtilities,
        ClassnameUtilities $classnameUtilities)
    {
        $this->serverType = $serverType;
        $this->context = $context;
        $this->cachingEnabled = $this->serverType == 'production';
        $this->themeUtilities = $themeUtilities;
        $this->pathUtilities = $pathUtilities;
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @return string
     */
    public function getServerType()
    {
        return $this->serverType;
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
     * @return boolean
     */
    public function getCachingEnabled()
    {
        return $this->cachingEnabled;
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
     * @param string $serverType
     */
    public function setServerType($serverType)
    {
        $this->serverType = $serverType;
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
     * @param boolean $cachingEnabled
     */
    public function setCachingEnabled($cachingEnabled)
    {
        $this->cachingEnabled = $cachingEnabled;
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
     * @return \Chamilo\Libraries\File\Path
     */
    public function getPathUtilities()
    {
        return $this->pathUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function setPathUtilities($pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
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
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilties
     */
    public function setClassnameUtilties($classnameUtilties)
    {
        $this->classnameUtilties = $classnameUtilties;
    }

    static public function launch()
    {
        $type = Request :: get(self :: PARAM_TYPE, null);

        if ($type)
        {
            $classname = __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() .
                 'Utilities';

            if (class_exists($classname))
            {
                $theme = Request :: get(self :: PARAM_THEME);
                $serverType = Request :: get(self :: PARAM_SERVER_TYPE, 'production');
                $context = Request :: get(self :: PARAM_CONTEXT, __NAMESPACE__);

                $themeUtilities = new Theme(
                    $theme,
                    StringUtilities :: getInstance(),
                    ClassnameUtilities :: getInstance(),
                    Path :: getInstance());
                $pathUtilities = Path :: getInstance();
                $classnameUtilities = ClassnameUtilities :: getInstance();

                $utilities = new $classname($serverType, $context, $themeUtilities, $pathUtilities, $classnameUtilities);
                $utilities->run();
            }
        }
    }
}
