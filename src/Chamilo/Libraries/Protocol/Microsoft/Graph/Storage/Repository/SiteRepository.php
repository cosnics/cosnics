<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Model\FieldValueSet;
use Microsoft\Graph\Model\ListInfo;
use Microsoft\Graph\Model\ListItem;
use Microsoft\Graph\Model\Site;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class SiteRepository
{
    private GraphRepository $graphRepository;

    /**
     * GroupRepository constructor.
     *
     * @param GraphRepository $graphRepository
     */
    public function __construct(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     * @param string $siteName
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\Site|null
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getSite(string $siteName)
    {
        try
        {
            return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/sites/' . $siteName,
                Site::class
            );
        }
        catch (GraphException $exception)
        {
            if ($exception->getCode() == 404)
            {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param string $siteId
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListInfo[]|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getLists(string $siteId)
    {
        return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
            '/sites/' . $siteId . '/lists',
            Site::class, true
        );
    }

    /**
     * @param string $siteId
     * @param string $listName
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListInfo|null
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getList(string $siteId, string $listName)
    {
        try
        {
            return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/sites/' . $siteId . '/lists/' . $listName,
                ListInfo::class
            );
        }
        catch (GraphException $exception)
        {
            if ($exception->getCode() == 404)
            {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param bool $showFields
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListItem[]|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getListItems(string $siteId, string $listId, string $filter = '', bool $showFields = true)
    {
        $url = '/sites/' . $siteId . '/lists/' . $listId . '/items';

        $parameters = [];
        if($showFields)
        {
            $parameters['expand'] = 'fields';
        }

        if($filter)
        {
            $parameters['$filter'] = $filter;
        }

        if(count($parameters) > 0)
        {
            $url .= '?' . http_build_query($parameters);
        }

        return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
            $url, ListItem::class, true
        );
    }

    /**
     * @param string $siteId
     * @param string $listName
     * @param string $itemId
     * @param bool $showFields
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListItem|null
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getListItem(string $siteId, string $listName, string $itemId, bool $showFields = true)
    {
        $url = '/sites/' . $siteId . '/lists/' . $listName . '/items/' . $itemId;
        if($showFields)
        {
            $url .= '?expand=fields';
        }

        try
        {
            return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                $url, ListItem::class
            );
        }
        catch (GraphException $exception)
        {
            if ($exception->getCode() == 404)
            {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param array $values
     *
     * @return \Microsoft\Graph\Model\Entity|\Microsoft\Graph\Model\ListItem|null
     * @throws GraphException
     */
    public function addListItem(string $siteId, string $listId, array $values)
    {
        $parameters = ['fields' => $values];
        $url = '/sites/' . $siteId . '/lists/' . $listId . '/items';

        return $this->graphRepository->executePostWithAccessTokenExpirationRetry(
            $url, $parameters, ListItem::class
        );
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param string $itemId
     * @param array $values
     *
     * @return \Microsoft\Graph\Model\Entity
     * @throws GraphException
     */
    public function updateListItem(string $siteId, string $listId, string $itemId, array $values)
    {
        $url = '/sites/' . $siteId . '/lists/' . $listId . '/items/' . $itemId . '/fields';

        return $this->graphRepository->executePatchWithAccessTokenExpirationRetry(
            $url, $values
        );
    }

    /**
     * @param string $siteId
     * @param string $listId
     * @param string $itemId
     *
     * @throws GraphException
     */
    public function deleteListItem(string $siteId, string $listId, string $itemId)
    {
        $url = '/sites/' . $siteId . '/lists/' . $listId . '/items/' . $itemId;

        $this->graphRepository->executeDeleteWithAccessTokenExpirationRetry($url);
    }

}
