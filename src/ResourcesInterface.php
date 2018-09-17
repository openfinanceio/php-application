<?php
namespace CFX;

/**
 * This class is a dependency injector. It is intended to be instaniated as one of the top-level
 * objects in an application and passed down to the various components of the application.
 *
 * While this most general interface only provides a logger, the extended versions typically in
 * use will provide a database connection, an MQ connection, an emailer, etc...
 */
interface ResourceInterface
{
    /**
     * Get an instance of a logger
     */
    public function getLogger(): \Psr\Log\LoggerInterface;
}
