<?php
/*
 * (c) Pekin Denis <dpekin@armd.ru>
 */

namespace Armd\DCXBundle\Exception;

/**
 * Thrown whenever a DCXParser process fails.
 */
class ParserException extends RuntimeException
{
    protected $message;

    public function __construct($xml){
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $this->message .= $this->xml_error($error, $xml);
        }
        libxml_clear_errors();
        parent::__construct($this->message);
    }

    private function xml_error($error, $xml){
        $err  = $xml[$error->line - 1] . "\n";
        $err .= str_repeat('-', $error->column) . "^\n";

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $err .= "Warning $error->code: ";
                break;
             case LIBXML_ERR_ERROR:
                $err .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $err .= "Fatal Error $error->code: ";
                break;
        }

        $err .= trim($error->message) .
                   "\n  Line: $error->line" .
                   "\n  Column: $error->column";

        if ($error->file) {
            $err .= "\n  File: $error->file";
        }
        return $err;
    }
}