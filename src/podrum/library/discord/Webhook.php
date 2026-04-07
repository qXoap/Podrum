<?php

namespace podrum\library\discord;

use pocketmine\Server;
use podrum\library\discord\async\DiscordMessageAsync;

final class Webhook
{
    public function __construct(
        protected string $url
    )
    {
    }

    public static function create(string $url): Webhook
    {
        return new Webhook($url);
    }

    public function isValid(): bool
    {
        return filter_var($this->url, FILTER_VALIDATE_URL) !== false;
    }

    public function getURL(): string
    {
        return $this->url;
    }

    public function send(Message $message): void
    {
        Server::getInstance()->getAsyncPool()->submitTask(new DiscordMessageAsync($this, $message));
    }
}