<?php
namespace CFX;


// General Runtime Exceptions

/**
 * A general exception from which all other CFX exceptions should be derived
 */
class Exception extends \RuntimeException {}

/**
 * An exception for debugging other exceptions (Exceptions that are usually caught can be wrapped in this exception,
 * which is usually thrown in development environments.)
 */
class DebugException extends \CFX\Exception { }

/**
 * An exception indicating that the requested functionality *should* be implemented but isn't yet.
 */
class UnimplementedFeatureException extends \CFX\Exception { }

/**
 * An exception thrown when an object doesn't have the necessary dependencies or data to execute the requested
 * functionality.
 */
class UnpreparedObjectException extends \CFX\Exception { }








// Protocol Exceptions (thrown when clients don't follow the specified request or authentication protocols)

class ProtocolException extends \CFX\Exception { }

/** The URL is not formatted correctly **/
class BadUriFormatException extends ProtocolException { }








// Data Domain Exceptions

/** 
 * UnknownDatasourceException
 * Indicates that the requested datasource is not known to the system
 */
class UnknownDatasourceException extends \CFX\Exception { }

/**
 * CorruptDataException
 * Indicates that the database contains bad or inconsistent data
 **/
class CorruptDataException extends \CFX\Exception { }

/**
 * ResourceNotFoundException
 * Someone has sought a resource using an id that's not in the database
 */
class ResourceNotFoundException extends \InvalidArgumentException { }

/**
 * UnknownResourceTypeException
 * The given context does not know how to deal with resources of the given type
 */
class UnknownResourceTypeException extends \CFX\Exception { }

/**
 * BadInputException
 * Exception specifying that the input data provided is malformed
 */
class BadInputException extends \InvalidArgumentException {
    protected $inputErrors = [];
    public function getInputErrors() { return $this->inputErrors; }
    public function setInputErrors($errors) {
        if (!is_array($errors)) throw new \CFX\Exception("Errors passed to `BadInputException::setInputErrors` must be an array of `\KS\JsonApi\ErrorInterface` objects.");
        foreach ($errors as $e) {
            if (!($e instanceof \CFX\JsonApi\ErrorInterface)) throw new \CFX\Exception("Errors passed to `BadInputException::setInputErrors` must be an array of `\KS\JsonApi\ErrorInterface` objects.");
        }
        $this->inputErrors = $errors;
        return $this;
    }
/*
    public function getMessage() {
        $msg = parent::getMessage();
        $msg .= "\n\nErrors:\n";
        foreach($this->inputErrors as $e) {
            $msg .= "\n  {$e->getTitle()}: {$e->getDetail()}";
        }
        return $msg;
    }
 */
}

/**
 * DuplicateResource
 * A submitted resource conflicts with one that's already in the database
 */
class DuplicateResourceException extends \CFX\Exception {
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
class UnidentifiedResourceException extends \CFX\Exception { }

/**
 * UninitializedResourceException
 * The requested functionality requires an initialized resource, but this resource has not been initialized yet.
 */
class UninitializedResourceException extends \CFX\Exception { }







// Authentication Exceptions

/** General Authn exception */
class AuthnException extends \CFX\Exception { }

/** Required credentials are missing */
class AuthnMissingCredentialsException extends AuthnException { }

/** The credentials passed are invalid. */
class AuthnInvalidCredentialsException extends AuthnException { }







// Authorization Exceptions

/**
 * General Unauthorized Access Exception
 */
class AuthzException extends \CFX\Exception { }

/**
 * A User (or Brokerage Partner on behalf of a user) has attempted to access functionality they are not allowed to access
 */
class AuthzUnauthorizedUserException extends AuthzException { }

/**
 * The credentials required for authorization are missing
 */
class AuthzMissingCredentialsException extends AuthzException { }

/**
 * The requested action requires an authenticated user
 */
class AuthzUnauthenticatedRequestException extends AuthzException { }









// Miscellaneous exceptions

class PathOverconsumedException extends \CFX\Exception { }

