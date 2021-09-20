<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class UserPresencesComponent extends Manager
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        $this->ensureUserIdentifier();
        $this->checkAccessRights();
        BreadcrumbTrail::getInstance()->remove(count(BreadcrumbTrail::getInstance()->getBreadcrumbs()) - 1);

        return $this->getTwig()->render(
            Manager::context() . ':UserPresences.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if ($this->getRightsService()->canUserViewPresence($this->getUser()))
        {
            return;
        }

        throw new NotAllowedException();
    }

    /**
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getTemplateProperties(): array
    {
        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'LANGUAGE' => $this->getTranslator()->getLocale(),
            'PRESENCE_TITLE' => $this->getPresence()->get_title()
        ];
    }

    public function render_header($pageTitle = '')
    {
        $html = [];
        $html[] = parent::render_header('');
        return implode(PHP_EOL, $html);
    }
}