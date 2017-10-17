<?php
namespace CFX;

abstract class AbstractDatasource implements DatasourceInterface {
    protected static $resourceType;
    protected $context;
    protected $currentData;

    public function __construct(DataContextInterface $context) {
        if (static::$resourceType == null) throw new \RuntimeException("Programmer: You need to define this subclient's `\$resourceType` attribute. This should match the type of resources that this client deals in.");
        $this->context = $context;
    }

    /**
     * @see DatasourceInterface::getCurrentData
     */
    public function getCurrentData() {
        $data = $this->currentData;
        $this->currentData = null;
        return $data;
    }

    public function save(\CFX\BaseResourceInterface $r) {
        // If we're trying to save with errors, throw exception
        if ($r->hasErrors()) {
            $e = new \CFX\BadInputException("Bad input");
            $e->setInputErrors($r->getErrors());
            throw $e;
        }

        // If it exists already, update it
        if ($r->getId()) $this->saveExisting($r);

        // Else, create it
        else $this->saveNew($r);

        return $this;
    }

    /**
     * inflateData -- use the provided data to create a Resource or ResourceCollection object
     *
     * @param array $data
     * @param bool $isCollection Whether or not this data represents a collection
     * @return \CFX\ResourceInterface|\CFX\ResourceCollectionInterface
     */
    protected function inflateData(array $obj, $isCollection) {
        foreach($obj as $k => $o) {
            $this->currentData = $o;
            $obj[$k] = $this->create();
            if ($this->currentData !== null) throw new \RuntimeException("There appears to be leftover data in the cache. You should make sure that all data objects call this database's `getCurrentData` method from within their constructors. (Offending class: `".get_class($obj[$k])."`. Did you overwrite the default constructor?)");
        }
        return $isCollection ?
            $f->newJsonApiResourceCollection($obj) :
            $obj[0]
        ;
    }

    /**
     * parseDSL -- parse the DSL query that the user sent in
     *
     * @param string $query A query written in a domain-specific query language
     * @return DSLQueryInterface
     */
    protected function parseDSL($query) {
        return GenericDSLQuery::parse($query);
    }


    /**
     * Convert data to JsonApi format
     *
     * @param string $type What to put for the `type` parameter
     * @param array $records The rows of data to convert
     * @param array $rels An optional list of relationships. Array should be indexed by FIELD NAME, and each item
     * should be an array whose first value is the `type` of object that the relationship deals with and whose
     * second value is the `name` of the relationship.
     * @param string $idField The name of the field that contains the object's ID (Default: 'id')
     * @return array A collection of resources represented in json api format
     */

    protected function convertToJsonApi($type, $records, $rels=[], $idField='id') {
        if (count($records) == 0) return $records;

        $jsonapi = [];
        foreach($records as $n => $r) {
            $jsonapi[$n] = [
                'type' => $type,
                'id' => $r[$idField],
                'attributes' => [],
            ];
            if (count($rels) > 0) $jsonapi[$n]['relationships'] = [];

            foreach ($r as $field => $v) {
                if ($field == $idField) continue;
                if (array_key_exists($field, $rels)) {
                    // For to-many relationships
                    if (is_array($v)) {
                        $rel = [];
                        foreach($v as $relId) {
                            $rel[] = [
                                "data" => [
                                    "type" => $rels[$field][0],
                                    "id" => $relId,
                                ],
                            ];
                        }

                    // For to-one relationships
                    } else {
                        if (!$v) $rel = [ "data" => null ];
                        else $rel = [
                            "data" => [
                                "type" => $rels[$field][0],
                                "id" => $v,
                            ]
                        ];
                    }

                    // Add relationship
                    $jsonapi[$n]['relationships'][$rels[$field][1]] = $rel;
                } else {
                    $jsonapi[$n]['attributes'][$field] = $v;
                }
            }
        }

        return $jsonapi;
    }







    // Factory methods for json api (passthrough to context)

    public function newJsonApiDocument($data=null) { return $this->context->newJsonApiDocument($data); }
    public function newJsonApiResource($data=null, $type=null, $validAttrs=null, $validRels=null) { return $this->context->newJsonApiResource($data, $type, $validAttrs, $validRels, $this); }
    public function newJsonApiRelationship($data) { return $this->context->newJsonApiRelationship($this, $data); }
    public function newJsonApiError($data) { return $this->context->newJsonApiError($this, $data); }
    public function newJsonApiResourceCollection($resources=[]) { return $this->context->newJsonApiResourceCollection($resources); }
    public function newJsonApiErrorsCollection($errors=[]) { return $this->context->newJsonApiErrorsCollection($errors); }
    public function newJsonApiMeta($data=null) { return $this->context->newJsonApiMeta($data); }
    public function newJsonApiLink($data=null) { return $this->context->newJsonApiLink($this, $data); }
    public function newJsonApiLinksCollection($links=[]) { return $this->context->newJsonApiLinksCollection($links); }

}

