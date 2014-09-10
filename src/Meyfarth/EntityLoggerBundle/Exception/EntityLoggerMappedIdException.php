<?php

namespace Meyfarth\EntityLoggerBundle\Exception;

use Exception;

/**
 * Exception thrown when an entity does not implements a getId() method
 *
 * @author Meyfarth
 */
class EntityLoggerMappedIdException extends Exception {
    
    public function __construct($message, $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
