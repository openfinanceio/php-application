<?php
namespace CFX\Test;

class SqlPeopleDatasource extends \CFX\AbstractDatasource implements \CFX\DatasourceInterface {
    protected static $resourceType = 'people';

    public function create(array $data=[]) {
        return new Person($this, $data);
    }

    public function get($q=null, $raw=false) {
        $query = $this->newSQLQuery([
            'action' => 'SELECT',
            'fields' => 'name, dob, bestFriend',
            'preposition' => 'FROM',
            'tables' => '`users`',
            'where' => null,
            'params' => [],
        ]);
        if ($q && substr($q, 0, 3) == 'id=') {
            $query->where = '`id` = ?';
            $query->params = array_merge($query->params, [substr($q, 3)]);
            $isCollection = false;
        } else {
            $isCollection = true;
        }

        $data = $this->db->executeQuery($query);
        return $raw ?
            $data :
            $this->inflateData($data, $isCollection)
        ;
    }

    public function save(\CFX\BaseResourceInterface $r) {
        // If we're trying to save with errors, throw exception
        if ($r->hasErrors()) {
            $e = new \CFX\BadInputException("Bad input");
            $e->setInputErrors($r->getErrors());
            throw $e;
        }

        $q = $this->newSQLQuery();

        // If it exists already
        if ($r->getId()) {
            $q->action = "UPDATE `{$r->getResourceType()}` SET";
            $q->fields = "`".implode("` = ?, `", array_keys($r->getChanges()))."` = ?";
            $q->where = '`id` = ?';
            $q->params = array_merge(array_values($r->getChanges()), $r->getId());
        } else {
            $r->setId(md5(uniqid()));
            $data = $r->jsonSerialize();
            $placeholders = ['?'];
            for($i = 0; $i < count($data['attributes']); $i++) $placeholders[] = '?';
            $q->action = "INSERT INTO `{$r->getResourceType()}` (`id`, `".implode('`, `', array_keys($data['attributes']))."`)";
            $q->fields = " VALUES (".implode(", ", $placeholders).")";
        }

        $this->db->executeQuery($q);
        return $this;
    }

    public function delete($r) {
        if (is_object($r) && !($r instanceof \CFX\BaseResourceInterface)) throw new \InvalidArgumentException("You must pass either an ID or a Resource into this function.");
        if (is_object($r)) $r = $r->getId();

        $this->db->executeQuery($this->newSQLQuery([
            'action' => 'DELETE',
            'preposition' => 'FROM',
            'tables' => '`users`',
            'where' => '`id` = ?',
            'params' => [$r],
        ]));

        return $this;
    }

    protected function newSQLQuery(array $q=[]) {
        return new \CFX\SQLQuery($q);
    }

    protected function inflateData(array $obj, $isCollection) {
        if (!$isCollection) $obj = [$obj];
        foreach($obj as $k => $o) {
            if ($this->currentData !== null) throw new \RuntimeException("There appears to be leftover data in the cache. You should make sure that all data objects call this database's `getCurrentData` method from within their constructors.");
            $this->currentData = $o;
            $obj[$k] = $this->create();
        }
        return $isCollection ?
            $f->newJsonApiResourceCollection($obj) :
            $obj[0]
        ;
    }
}

