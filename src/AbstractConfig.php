<?php
namespace CFX;

abstract class AbstractConfig extends \KS\BaseConfig implements ConfigInterface {
    protected $httpClient;
    protected $pdos = [];

    public function getDisplayErrors() { return $this->get('php-display-errors'); }
    public function getErrorLevel() { return $this->get('php-error-level'); }

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
}

