<?php
namespace CFX;


// General Runtime Exceptions

/**
 * An exception for debugging other exceptions (Exceptions that are usually caught can be wrapped in this exception,
 * which is usually thrown in development environments.)
 */
class DebugException extends \Exception { }

/**
 * An exception indicating that the requested functionality *should* be implemented but isn't yet.
 */
class UnimplementedFeatureException extends \RuntimeException { }

/**
 * An exception thrown when an object doesn't have the necessary dependencies or data to execute the requested
 * functionality.
 */
class UnpreparedObjectException extends \RuntimeException { }








// Protocol Exceptions (thrown when clients don't follow the specified request or authentication protocols)

class ProtocolException extends \RuntimeException { }








// Data Domain Exceptions

/** 
 * UnknownDatasourceException
 * Indicates that the requested datasource is not known to the system
 */
class UnknownDatasourceException extends \RuntimeException { }

/**
 * CorruptDataException
 * Indicates that the database contains bad or inconsistent data
 **/
class CorruptDataException extends \RuntimeException { }

/**
 * ResourceNotFoundException
 * Someone has sought a resource using an id that's not in the database
 */
class ResourceNotFoundException extends \InvalidArgumentException { }

/**
 * UnknownResourceTypeException
 * The given context does not know how to deal with resources of the given type
 */
class UnknownResourceTypeException extends \RuntimeException { }

/**
 * BadInputException
 * Exception specifying that the input data provided is malformed
 */
class BadInputException extends \InvalidArgumentException {
    protected $inputErrors = [];
    public function getInputErrors() { return $this->inputErrors; }
    public function setInputErrors($errors) {
        if (!is_array($errors)) throw new \RuntimeException("Errors passed to `BadInputException::setInputErrors` must be an array of `\KS\JsonApi\ErrorInterface` objects.");
        foreach ($errors as $e) {
            if (!($e instanceof \KS\JsonApi\ErrorInterface)) throw new \RuntimeException("Errors passed to `BadInputException::setInputErrors` must be an array of `\KS\JsonApi\ErrorInterface` objects.");
        }
        $this->inputErrors = $errors;
        return $this;
    }
}

/**
 * DuplicateResource
 * A submitted resource conflicts with one that's already in the database
 */
class DuplicateResourceException extends \RuntimeException {
    protected $duplicate;
    public function setDuplicateResource(\KS\JsonApi\BaseResourceInterface $resource) {
        $this->duplicate = $resource;
    }
    public function getDuplicateResource() { return $this->duplicate; }
}

/**
 * UnidentifiedResourceException
 * There was an attempt to fetch resource data from the database, but the given resource lacked an id
 */
class UnidentifiedResourceException extends \RuntimeException { }

/**
 * UninitializedResourceException
 * The requested functionality requires an initialized resource, but this resource has not been initialized yet.
 */
class UninitializedResourceException extends \RuntimeException { }







// Authentication Exceptions

/** General Authn exception */
class AuthnException extends \RuntimeException { }

/** The credentials passed are invalid. */
class AuthnInvalidCredentialsException extends AuthnException { }







// Authorization Exceptions

/**
 * General Unauthorized Access Exception
 */
class AuthzException extends \RuntimeException { }

/**
 * A User (or Brokerage Partner on behalf of a user) has attempted to access functionality they are not allowed to access
 */
class AuthzUnauthorizedUserException extends AuthzException { }

/**
 * The credentials required for authorization are missing
 */
class AuthzMissingCredentialsException extends AuthzException { }









// Miscellaneous exceptions

class PathOverconsumedException extends \RuntimeException { }

