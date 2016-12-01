<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\File\PathBuilder;

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
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @param string $context
     * @param Theme $themeUtilities
     * @param Path $pathUtilities
     * @param ClassnameUtilities $classnameUtilities
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct($context = __NAMESPACE__, Theme $themeUtilities, Path $pathUtilities,
        ClassnameUtilities $classnameUtilities, \Symfony\Component\HttpFoundation\Request $request)
    {
        $this->context = $context;
        $this->themeUtilities = $themeUtilities;
        $this->pathUtilities = $pathUtilities;
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
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
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

    static public function launch(\Symfony\Component\HttpFoundation\Request $request)
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

                $classnameUtilities = ClassnameUtilities::getInstance();

                $themeUtilities = new Theme(
                    $theme,
                    StringUtilities::getInstance(),
                    ClassnameUtilities::getInstance(),
                    new PathBuilder($classnameUtilities));

                $utilities = new $classname(
                    $context,
                    $themeUtilities,
                    Path::getInstance(),
                    $classnameUtilities,
                    $request);

                $utilities->run();
            }
        }
    }
}
