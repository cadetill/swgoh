<?php

namespace Im\Shared\Infrastructure\Telegram;

use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Request;

class TelegramInteractiveMessage
{
    private int $chatId;
    private int $triggerMessageId;
    private int $lastMessageSentId;

    public function __construct(int $chatId, int $triggerMessageId)
    {
        $this->chatId           = $chatId;
        $this->triggerMessageId = $triggerMessageId;
    }

    public function start(string $message)
    {
        $this->lastMessageSentId = $this->sendMessage($message);
        $this->showLoading();
    }

    public function update(string $message)
    {
        $this->assertIsStarted();
        $this->updateMessage($message);
        $this->showLoading();
    }

    public function finish()
    {
        $this->assertIsStarted();
        $this->deleteMessage();
    }

    private function showLoading(): void
    {
        Request::sendChatAction(
            [
                'chat_id' => $this->chatId,
                'action'  => ChatAction::TYPING,
            ]
        );
    }

    private function sendMessage(string $message): int
    {
        $response = Request::sendMessage(
            [
                'chat_id'             => $this->chatId,
                'text'                => $message,
                'parse_mode'          => 'html',
                'reply_to_message_id' => $this->triggerMessageId,
            ]
        );

        return $response->getResult()->message_id;
    }

    private function updateMessage(string $message): void
    {
        Request::editMessageText(
            [
                'chat_id'    => $this->chatId,
                'text'       => $message,
                'parse_mode' => 'html',
                'message_id' => $this->lastMessageSentId,
            ]
        );
    }

    private function deleteMessage()
    {
        Request::deleteMessage(
            [
                'chat_id'    => $this->chatId,
                'message_id' => $this->lastMessageSentId,
            ]
        );
    }

    private function assertIsStarted()
    {
        if (is_null($this->lastMessageSentId)) {
            $this->start('_');
        }
    }
}
