<?php

namespace Sfynx\DddBundle\Layer\Infrastructure\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Message extends Constraint
{
    protected $message = "The Message %string% contains an illegal character.";

    protected $regex = '/^[\w \-\']{1,255}$/i';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @param string $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
