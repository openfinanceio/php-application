<?php
namespace CFX\Test;

class DataContext implements DataContextInterface {
    protected $f;
    protected $db = [];
    protected $clients = ['users'];

    public function __construct(Config $cnf) {
        $pdos = $cnf->getCFXPdos();
        if (!is_array($pdos)) $pdos = array('default' => $pdos);

        foreach($pdos as $k => $pdo) {
            if (!($pdo instanceof \Closure) && !($pdo instanceof \PDO)) throw new \InvalidArgumentException("Any PDO objects that you send must either be PDOs or closures that instantiate PDOs on demand.");
            $this->db[$k] = $pdo;
        }

        $clients = [];
        foreach($this->clients as $n) $clients[$n] = null;
        $this->clients = $clients;
    }

    /**
     * Gets a data connection by name. If the named connection is not found, throws an
     * InvalidArgumentException. If the named connection is found, but is a closure,
     * converts the closure to a PDO, possibly throwing a RuntimeException if the object
     * returned by the closure is not a PDO.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \PDO|\CFX\Test\PDO
     */
    protected function db($k='default') {
        if (!array_key_exists($k, $this->db)) throw new \InvalidArgumentException("Datasource '$k' wasn't provided by the configuration. To use this datasource, you must provide it in the \$cnf['pdos'] array, indexed by '$k'. It may be either a PDO instance itself or a closure that instantiates a PDO instance. For example:\n\n```php\n\$cnf['pdos']['$k'] = function() {\n    return new \PDO('mysql:host=unix_socket=/var/run/mysql/mysql.sock;dbname=cfxtrading_com');\n}\n```\n"); 

        if ($this->db[$k] instanceof \Closure) {
            $this->db[$k] = $this->db[$k]($this->f);
            if (!($this->db[$k] instanceof \PDO) && !($this->db[$k] instanceof \CFX\Test\PDO)) throw new \RuntimeException("The closure provided for datasource '$k' did not return a valid PDO or Test PDO! Please investigate this in your configuration files, under the `['cfx-pdos'][$k]` key.");
        }

        return $this->db[$k];
    }

    public function getDb($k='default') {
        return $this->db($k);
    }

    public function executeQuery(\CFX\SQLQueryInterface $query) {
        $q = $this->db($query->database)->prepare($query->query);
        $q->execute($query->params);

        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function __get($name) {
        if (!array_key_exists($name, $this->clients)) throw new \RuntimeException("Programmer: client `$name` doesn't exist on this database.");
        if ($this->clients[$name] === null) $this->clients[$name] = $this->instantiateClient($name);
        return $this->clients[$name];
    }

    protected function instantiateClient($name) {
        if ($name == 'users') return new UsersDatabase($this);
        throw new \RuntimeException("Don't know how to create `$name` clients. Please add `$name` to the factory method `instantiateClient`");
    }
}

