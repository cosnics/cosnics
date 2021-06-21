<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\SiteRepository;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class SiteService
{
    protected SiteRepository $siteRepository;

    public function __construct(SiteRepository $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    /**
     * @param string $siteName
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\Site|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getSite(string $siteName)
    {
        return $this->siteRepository->getSite($siteName);
    }

    /**
     * @param string $siteId
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListInfo[]|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getLists(string $siteId)
    {
        return $this->siteRepository->getLists($siteId);
    }

    /**
     * @param string $siteId
     * @param string $listTitle
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListInfo|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getList(string $siteId, string $listTitle)
    {
        return $this->siteRepository->getList($siteId, $listTitle);
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param bool $showFields
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListItem[]|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getListItems(string $siteId, string $listId, bool $showFields = true)
    {
        return $this->siteRepository->getListItems($siteId, $listId, $showFields);
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param string $itemId
     * @param bool $showFields
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListItem|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getListItem(string $siteId, string $listId, string $itemId, bool $showFields = true)
    {
        return $this->siteRepository->getListItem($siteId, $listId, $itemId, $showFields);
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param array $values
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListItem|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function addListItem(string $siteId, string $listId, array $values)
    {
        return $this->siteRepository->addListItem($siteId, $listId, $values);
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param string $itemId
     * @param array $values
     *
     * @return \Microsoft\Graph\Model\Entity
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function updateListItem(string $siteId, string $listId, string $itemId, array $values)
    {
        return $this->siteRepository->updateListItem($siteId, $listId, $itemId, $values);
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param string $itemId
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function deleteListItem(string $siteId, string $listId, string $itemId)
    {
        $this->siteRepository->deleteListItem($siteId, $listId, $itemId);
    }

}
