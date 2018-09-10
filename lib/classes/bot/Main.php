<?php
/**
 * Created by PhpStorm.
 * User: pembr
 * Date: 19.08.2018
 * Time: 23:25
 */

namespace Telebot\Lib\Bot;

use DateTime;
use PDO;
use Telebot\Lib\Config\Config;
use Telebot\Lib\DB\Database;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Main extends Bot
{
    private $db = null;

    private $botCreator;
    protected $bot;
    protected $body;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->bot = new Client(Config::get('token'));
        $this->body = json_decode($this->bot->getRawBody(), true);
        $this->botCreator = Config::get('bot_creator');
    }

    public function index()
    {
        /** @var BotApi $bot */
        $bot = $this->bot;
        $body = $this->body;
        ob_flush();
        ob_start();
        print_r($body);
        file_put_contents('test-button-click.txt', ob_get_flush());

        if (isset($body['callback_query'])) {
            if (mb_strpos($body['callback_query']['data'], "send_track_") !== false) {
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                    [
                        [
                            [
                                'text' => 'Ответить',
                                'callback_data' => 'hit'
                            ]
                        ]
                    ]
                );
                $bot->sendAudio('-1001136482619', 'CQADAgADFQQAAjfOoUjWF96NFtnaRwI', null, null, 'Слушаем', null, null);
                $bot->sendMessage('-1001136482619', 'Слушаем', null, false, null, $keyboard);
            } else {
                $chatId = $body['callback_query']['message']['chat']['id'];
                $callbackQueryId = $body['callback_query']['id'];
                $userId = $body['callback_query']['from']['id'];
                $messageId = $body['callback_query']['message']['message_id'];
                $originalText = $body['callback_query']['message']['text'] == "Слушаем" ? "Готов ответить:" : $body['callback_query']['message']['text'];
                $username = $body['callback_query']['from']['username'];

                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                    [
                        [
                            [
                                'text' => 'Ответить',
                                'callback_data' => 'hit'
                            ]
                        ]
                    ]
                );

                $bot->editMessageText($chatId, $messageId, "{$originalText}\n{$username}", null, false, $keyboard);
                $bot->answerCallbackQuery($callbackQueryId, "Ожидайте команды ведущего");
                $bot->restrictChatMember($chatId, $userId, null, true, true, true, true);
            }
//            $bot->sendMessage($chatId, "Отвечает @{$username}");
        }

        if ($body['message']['text'] == '📤 Отправить') {

            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                [
                    [
                        [
                            'text' => 'Ответить',
                            'callback_data' => 'hit'
//                            'switch_inline_query' => 'asdd'
                        ]
                    ]
                ]
            );
            $bot->sendAudio('-1001136482619', 'CQADAgADFQQAAjfOoUjWF96NFtnaRwI', null, null, 'Слушаем', null, null);
            $bot->sendMessage('-1001136482619', 'Слушаем', null, false, null, $keyboard);
        }

        if ($body['message']['text'] == '📖 Список') {

            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                [
                    [
                        [
                            'text' => 'Track 1',
                            'callback_data' => 'send_track_1'
                        ],
                    ],
                    [
                        [
                            'text' => 'Track 2',
                            'callback_data' => 'send_track_2'
                        ],
                    ],
                    [
                        [
                            'text' => 'Track 3',
                            'callback_data' => 'send_track_3'
                        ],
                    ],
                    [
                        [
                            'text' => 'Track 4',
                            'callback_data' => 'send_track_4'
                        ],
                    ],
                    [
                        [
                            'text' => 'Track 5',
                            'callback_data' => 'send_track_5'
                        ],
                    ]
                ]
            );
            $bot->sendMessage($body['message']['chat']['id'], 'Список загруженных песен', null, false, null, $keyboard);
        }

        $bot->command('start', function ($message) use ($bot) {
            /** @var Message $message */
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array(array("📖 Список", "📤 Отправить")), true, true); // true for one-time keyboard

            $answer = "Добро пожаловать!";
            $bot->sendMessage($message->getChat()->getId(), $answer, null, false, null, $keyboard);
//            $bot->sendMessage('-1001136482619', $answer, null, false, null, $keyboard);
        });

        $bot->run();
    }
}