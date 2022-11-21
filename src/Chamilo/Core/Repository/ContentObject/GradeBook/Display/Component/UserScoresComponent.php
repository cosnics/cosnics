<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookUserJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookAjaxService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class UserScoresComponent extends Manager
{
    /**
     * @var User|null
     */
    protected $user = null;

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

        $this->checkAccessRights();

        BreadcrumbTrail::getInstance()->remove(count(BreadcrumbTrail::getInstance()->getBreadcrumbs()) - 1);

        if ($this->getRightsService()->canUserEditGradeBook())
        {
            $user = $this->getUserEntity();
            $title = $this->getTranslator()->trans('GradeBook', [], Manager::context()) . ' ' . $user->get_fullname();
            BreadcrumbTrail::getInstance()->add(new Breadcrumb($this->get_url([self::PARAM_USER_ID => $user->getId()]), $title));
        }

        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\GradeBook\Display\Manager::context() . ':UserScores.html.twig',
            $this->getTemplateProperties()
        );
    }

    protected function getUserEntity()
    {
        if (isset($this->user)) {
            return $this->user;
        }
        if ($this->getRightsService()->canUserEditGradeBook())
        {
            $userId = $this->getRequest()->getFromPostOrUrl(self::PARAM_USER_ID);
            $this->user = $this->getUserService()->findUserByIdentifier($userId);
        }
        else
        {
            $this->user = $this->getUser();
        }
        return $this->user;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        return;
        throw new NotAllowedException();
    }

    /**
     * @return GradeBookAjaxService
     */
    protected function getGradeBookAjaxService()
    {
        return $this->getService(GradeBookAjaxService::class);
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return $this->getService(UserService::class);
    }

    protected function getTemplateProperties(): array
    {
        $gradebook = $this->getGradeBook();
        $gradeBookData = $this->getGradeBookService()->getGradeBook($gradebook->getActiveGradeBookDataId(), null);
        $gradebookItems = $this->getGradeBookServiceBridge()->findPublicationGradeBookItems();
        $this->getGradeBookAjaxService()->updateGradeBookData($gradeBookData, $gradebookItems);

        $user = $this->getUserEntity();
        $users = [GradeBookUserJSONModel::fromUser($user)];
        $userScores = $this->getGradeBookService()->getGradeBookScoresByUserId($gradeBookData, $user->getId());

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $userScores->toArray());

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'LANGUAGE' => $this->getTranslator()->getLocale(),
            'CONTENT_OBJECT_TITLE' => $gradebook->get_title(),
            'CAN_EDIT_GRADEBOOK' => $this->getRightsService()->canUserEditGradeBook(),
            'GRADEBOOK' => $this->getSerializer()->serialize($gradeBookData->toJSONModel(), 'json'),
            'USERS' => $this->getSerializer()->serialize($users, 'json'),
            'SCORES' => $this->getSerializer()->serialize($scores, 'json'),
            'USER_FULLNAME' => $this->user->get_fullname(),
            'GRADEBOOK_ROOT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => null
                ]
            )
        ];
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        if (empty($this->serializer))
        {
            $this->serializer = SerializerBuilder::create()
                ->setSerializationContextFactory(function () {
                    return SerializationContext::create()
                        ->setSerializeNull(true);
                })
                ->setDeserializationContextFactory(function () {
                    return DeserializationContext::create();
                })
                ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
                ->build();
        }
        return $this->serializer;
    }

    public function render_header($pageTitle = '')
    {
        $html = [];
        $html[] = parent::render_header('');

        return implode(PHP_EOL, $html);
    }
}
