<?php
namespace CFX;

abstract class AbstractDataContext implements DataContextInterface, \KS\JsonApi\ContextInterface {
    /**
     * Cache of child datasources
     */
    protected $datasources;

    /**
     * Convenience method for turning datasource "getter" methods into read-only properties
     */
    public function __get($name) {
        if (!array_key_exists($name, $this->datasources)) $this->datasources[$name] = $this->instantiateDatasource($name);
        return $this->datasources[$name];
    }

    /**
     * Instantiate a client with the given `$name`
     */
    protected function instantiateDatasource($name) {
        throw new UnknownDatasourceException("Programmer: Don't know how to handle datasources of type `$name`. If you'd like to handle this, you should either add this datasource to the `instantiateDatasource` method in this class or create a derivative class to which to add it.");
    }






    // Factory methods for json api

    public function newJsonApiDocument($data=null) { return new \KS\JsonApi\Document($this, $data); }
    public function newJsonApiResource($data=null, $type=null, $validAttrs=null, $validRels=null, DatasourceInterface $datasource=null) {
        if ($type == 'assets') return new Asset($datasource, $data, $validAttrs, $validRels);
        if ($type == 'generic') return new \KS\JsonApi\GenericResource($datasource, $data, $validAttrs, $validRels);

        throw new \KS\JsonApi\UnknownResourceTypeException("Type `$type` is unknown. You can handle this type by overriding the `newJsonApiResource` method in your factory and adding a handler for the type there.");
    }
    public function convertJsonApiResource(\KS\JsonApi\ResourceInterface $src, $conversionType) {
        try {
            $type = explode('-', $src->getResourceType());
            for($i = 0; $i < count($type); $i++) $type[$i] = ucfirst($type[$i]);
            $type = implode('', $type);

            $client = $this->$type;
            return $client->convert($src, $conversionType);
        } catch (UnknownDatasourceException $e) {
            throw new UnknownResourceTypeException("Programmer: You've tried to convert a resource of type `{$src->getResourceType()}` to it's `$conversionType` format, but this data context (`".get_class($this)."`) doesn't know how to handle this type of resource.");
        }
    }
    public function newJsonApiRelationship($data) { return new \KS\JsonApi\Relationship($this, $data); }
    public function newJsonApiError($data) { return new \KS\JsonApi\Error($this, $data); }
    public function newJsonApiResourceCollection($resources=[]) { return new \KS\JsonApi\ResourceCollection($resources); }
    public function newJsonApiErrorsCollection($errors=[]) { return new \KS\JsonApi\ErrorsCollection($errors); }
    public function newJsonApiMeta($data=null) { return new \KS\JsonApi\Meta($data); }
    public function newJsonApiLink($data=null) { return new \KS\JsonApi\Link($this, $data); }
    public function newJsonApiLinksCollection($links=[]) { return new \KS\JsonApi\LinksCollection($links); }
}

