<?php

namespace Im\Shared\Exception;

use ErrorException;
use Longman\TelegramBot\Request;
use TData;
use Throwable;
use TTranslate;

class ExceptionHandler
{
    private TData      $data;
    private TTranslate $translate;

    public function __construct(TData $data)
    {
        $this->data      = $data;
        $this->translate = new TTranslate($this->data->language);
    }

    public function handleError(int $level, string $message, string $file = '', int $line = 0, array $context = [])
    {
        if ($this->isDeprecation($level)) {
            $this->handleDeprecation($message, $file, $line, $context);

            return;
        }

        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleException(Throwable $ex): void
    {
        if ($ex instanceof ImException) {
            $this->sendMessage($this->data->chatId, $ex->getMessage());

            return;
        }

        if ($this->data->localMode) {
            throw $ex;
        }

        $reportTemplate = <<<EOF
            <b>New Exception</b>:
               - User: <code>%s</code>
               - Command: <code>%s</code>
               - Message: <code>%s</code>
               - Trace: 
            <code>%s</code>
        EOF;

        $reportMessage = sprintf(
            $reportTemplate,
            join('|', [ $this->data->username, $this->data->firstname, $this->data->allycode ]),
            trim($this->data->message),
            $ex->getMessage(),
            $ex->getTraceAsString()
        );

        $this->sendMessage($this->data->debugChatId, $reportMessage);
        $this->sendMessage($this->data->chatId, $this->translate->getTransText('error8'), $this->data->messageId);
    }

    private function isDeprecation($level)
    {
        return in_array($level, [ E_DEPRECATED, E_USER_DEPRECATED ]);
    }

    private function handleDeprecation(string $message, string $file, int $line, array $context): void
    {
        $reportTemplate = <<<EOF
            <b>Deprecation</b>:
               - Message: <code>%s</code>
               - File: <code>%s</code>
        EOF;

        $reportMessage = sprintf(
            $reportTemplate,
            $message,
            $file . ':' . $line
        );

        $this->sendMessage($this->data->debugChatId, $reportMessage);
    }

    private function sendMessage(string $chatId, string $content, string $replyTo = null)
    {
        if ($this->data->localMode) {
            echo '<pre>';
            echo print_r($content, true);
            echo '</pre>';

            return;
        }

        Request::sendMessage(
            [
                'chat_id'             => $chatId,
                'text'                => $content,
                'parse_mode'          => 'html',
                'reply_to_message_id' => $replyTo,
            ]
        );
    }
}
