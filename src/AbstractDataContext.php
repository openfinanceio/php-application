<?php
namespace CFX;

abstract class AbstractDataContext implements DataContextInterface, \KS\JsonApi\FactoryInterface {
    /**
     * A list of valid clients
     */
    protected $clients = [];

    /**
     * Convenience method for turning datasource "getter" methods into read-only properties
     */
    public function __get($name) {
        if (!array_key_exists($name, $this->clients)) $this->clients[$name] = $this->instantiateClient($name);
        return $this->clients[$name];
    }

    /**
     * Instantiate a client with the given `$name`
     */
    abstract protected function instantiateClient($name);







    // Factory methods for json api

    public function newJsonApiDocument($data=null) { return new \KS\JsonApi\Document($this, $data); }
    public function newJsonApiResource($data=null, $type=null, $validAttrs=null, $validRels=null, DatasourceInterface $datasource=null) {
        if ($type == 'assets') return new Asset($datasource, $data, $validAttrs, $validRels);
        if ($type == 'generic') return new \KS\JsonApi\GenericResource($datasource, $data, $validAttrs, $validRels);

        throw new \KS\JsonApi\UnknownResourceTypeException("Type `$type` is unknown. You can handle this type by overriding the `newJsonApiResource` method in your factory and adding a handler for the type there.");
    }
    public function newJsonApiRelationship($data) { return new \KS\JsonApi\Relationship($this, $data); }
    public function newJsonApiError($data) { return new \KS\JsonApi\Error($this, $data); }
    public function newJsonApiResourceCollection($resources=[]) { return new \KS\JsonApi\ResourceCollection($resources); }
    public function newJsonApiErrorsCollection($errors=[]) { return new \KS\JsonApi\ErrorsCollection($errors); }
    public function newJsonApiMeta($data=null) { return new \KS\JsonApi\Meta($data); }
    public function newJsonApiLink($data=null) { return new \KS\JsonApi\Link($this, $data); }
    public function newJsonApiLinksCollection($links=[]) { return new \KS\JsonApi\LinksCollection($links); }
}

