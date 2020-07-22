<?php


namespace App\Service\Parser;


class CommentParser implements Parser
{
    private $id = "";

    private $message = "";

    public function parse($message) : self
    {
        if($message == "") {
            throw new \Exception("Message is empty");
        }
        $matches = [];
        preg_match("/^#[0-9]+ /", $message, $matches);
        if ([] === $matches) {
            $this->message = $message;

            return $this;
        }

        $this->id = trim(explode("#", $matches[0])[1]);
        $this->message = str_replace($matches[0] . ' ', "", $message);

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getMessage() : string
    {
        return $this->message;
    }
}