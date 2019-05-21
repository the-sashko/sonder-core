<?php
interface IPushProvider
{
    public function sendMessage(string $message) : IPushResponse;

    public function getHTMLInclude() : string;
}