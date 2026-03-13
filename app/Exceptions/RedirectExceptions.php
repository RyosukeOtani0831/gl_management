<?php
 
namespace App\Exceptions;
 
use Exception;
 
class RedirectExceptions extends Exception
{
    public $redirectTo;
    public $message;
 
    public function __construct(string $redirectTo, string $message)
    {
        # リダイレクトさせたい場所を指定。
        $this->redirectTo = $redirectTo;
        # リダイレクト先で表示するメッセージを指定。
        $this->message = $message;
    }
}