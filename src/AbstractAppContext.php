<?php
namespace CFX;

abstract class AbstractAppContext extends \KS\BaseConfig implements AppContextInterface {
    protected $httpClient;
    protected $pdos = [];

    public function getDisplayErrors() { return $this->get('php-display-errors'); }
    public function getErrorLevel() { return $this->get('php-error-level'); }




    /***
     * The following methods are not explicitly required by the context interface, but fulfill other context
     * requirements that are common enough to include here anyway.
     ***/
    public function getPdo($name='default') {
        if (!array_key_exists($name, $this->pdos)) {
            $pdos = $this->get('cfx-pdos');

            if (!array_key_exists($name, $pdos)) throw new \InvalidArgumentException("PDO named `$name` not configured!. Please add it to your config file located at `$this->defaultFile` and overridable with `$this->localFile`.");

            $pdo = $pdos[$name];
            if ($pdo instanceof \Closure) $pdo = $pdo($this);
            if (!($pdo instanceof \PDO || $pdo instanceof \CFX\Test\PDO)) throw new \RuntimeException("The object provided for datasource '$k' did not return a valid PDO or Test PDO! Please investigate this in your configuration files, under the `['cfx-pdos'][$k]` key. Configuration files are found at `$this->defaultFile` and `$this->localFile`.");

            $this->pdos[$name] = $pdo;
        }
        return $this->pdos[$name];
    }

    public function getHttpClient() {
        if (!$this->httpClient) {
            $client = $this->get('http-client');
            if ($client instanceof \Closure) $this->httpClient = $client($this);
            else $this->httpClient = $client;
        }
        return $this->httpClient;
    }





    // Factory methods for json api

    public function newJsonApiDocument($data=null) { return new \KS\JsonApi\Document($this, $data); }
    public function newJsonApiResource($data=null, $type=null, $validAttrs=null, $validRels=null, DatasourceInterface $datasource=null) {
        return new \KS\JsonApi\GenericResource($datasource, $data, $validAttrs, $validRels);
    }
    public function newJsonApiRelationship($data) { return new \KS\JsonApi\Relationship($this, $data); }
    public function newJsonApiError($data) { return new \KS\JsonApi\Error($this, $data); }
    public function newJsonApiResourceCollection($resources=[]) { return new \KS\JsonApi\ResourceCollection($resources); }
    public function newJsonApiErrorsCollection($errors=[]) { return new \KS\JsonApi\ErrorsCollection($errors); }
    public function newJsonApiMeta($data=null) { return new \KS\JsonApi\Meta($data); }
    public function newJsonApiLink($data=null) { return new \KS\JsonApi\Link($this, $data); }
    public function newJsonApiLinksCollection($links=[]) { return new \KS\JsonApi\LinksCollection($links); }
    public function getCurrentData() { return null; }
}

