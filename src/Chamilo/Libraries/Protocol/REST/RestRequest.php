<?php

namespace Chamilo\Libraries\Protocol\REST;

/**
 * @package Chamilo\Libraries\Protocol\REST;
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RestRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';

    protected string $method;
    protected string $path;
    protected ?string $modelClassName = null;
    protected array $urlParameters;
    protected array $queryParameters;
    protected array $bodyParameters;
    protected ?object $bodyObject;
    protected array $headers;
    protected bool $returnsMultipleRecords;

    public function __construct(
        string $method, string $path, string $modelClassName = null, array $urlParameters = array(),
        bool $returnsMultipleRecords = true, array $bodyParameters = array(), array $queryParameters = array(),
        array $headers = array(), object $bodyObject = null
    )
    {
        $this->setMethod($method);
        $this->setPath($path);
        $this->setUrlParameters($urlParameters);
        $this->setBodyParameters($bodyParameters);
        $this->setQueryParameters($queryParameters);
        $this->setReturnsMultipleRecords($returnsMultipleRecords);
        $this->setModelClassName($modelClassName);
        $this->setHeaders($headers);
        $this->setBodyObject($bodyObject);
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return RestRequest
     */
    public function setMethod(string $method): RestRequest
    {
        if (!in_array($method, $this->getAllowedMethods()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given method should be one of the allowed methods (%s)',
                    implode(', ', $this->getAllowedMethods())
                )
            );
        }

        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return RestRequest
     */
    public function setPath(string $path): RestRequest
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrlParameters(): ?array
    {
        return $this->urlParameters;
    }

    /**
     * @param array $urlParameters
     *
     * @return RestRequest
     */
    public function setUrlParameters(array $urlParameters): RestRequest
    {
        $this->urlParameters = $urlParameters;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return RestRequest
     */
    public function addUrlParameter(string $key, string $value)
    {
        $this->urlParameters[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryParameters(): ?array
    {
        return $this->queryParameters;
    }

    /**
     * @param array $queryParameters
     *
     * @return RestRequest
     */
    public function setQueryParameters(array $queryParameters): RestRequest
    {
        $this->queryParameters = $queryParameters;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return RestRequest
     */
    public function addQueryParameter(string $key, string $value)
    {
        $this->queryParameters[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getBodyParameters(): ?array
    {
        return $this->bodyParameters;
    }

    /**
     * @param array $bodyParameters
     *
     * @return RestRequest
     */
    public function setBodyParameters(array $bodyParameters): RestRequest
    {
        $this->bodyParameters = $bodyParameters;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return RestRequest
     */
    public function addBodyParameter(string $key, string $value)
    {
        $this->bodyParameters[$key] = $value;

        return $this;
    }

    public function getBodyObject(): ?object
    {
        return $this->bodyObject;
    }

    public function setBodyObject(?object $bodyObject): RestRequest
    {
        $this->bodyObject = $bodyObject;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReturnsMultipleRecords(): ?bool
    {
        return $this->returnsMultipleRecords;
    }

    /**
     * @param bool $returnsMultipleRecords
     *
     * @return RestRequest
     */
    public function setReturnsMultipleRecords(bool $returnsMultipleRecords): RestRequest
    {
        $this->returnsMultipleRecords = $returnsMultipleRecords;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelClassName(): ?string
    {
        return $this->modelClassName;
    }

    /**
     * @param string $modelClassName
     *
     * @return RestRequest
     */
    public function setModelClassName(string $modelClassName = null): RestRequest
    {
        if (empty($modelClassName))
        {
            return $this;
        }

        if (!class_exists($modelClassName))
        {
            throw new \RuntimeException(sprintf('Could not find the rest model class %s', $modelClassName));
        }

        $this->modelClassName = $modelClassName;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return RestRequest
     */
    public function setHeaders(array $headers): RestRequest
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return [self::METHOD_DELETE, self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_PATCH];
    }

    /**
     * @return string
     */
    public function getFullPath(): ?string
    {
        $path = $this->getPath();

        foreach ($this->urlParameters as $urlParameter => $value)
        {
            $path = str_replace('{' . $urlParameter . '}', $value, $path);
        }

        if(!empty($this->queryParameters))
        {
            $path .= '?' . http_build_query($this->queryParameters);
        }

        return $path;
    }
}
