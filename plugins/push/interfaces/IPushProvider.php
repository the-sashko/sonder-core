<?php
interface IPushProvider
{
    public function sendMessage(
        string $message,
        string $title,
        string $image,
        string $url
    ): IPushResponse;

    public function getHTMLSnippet(): string;
}
