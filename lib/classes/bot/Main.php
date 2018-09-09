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
use TelegramBot\Api\Client;
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
        $bot = $this->bot;
        $body = $this->body;

        $bot->command('start', function ($message) use ($bot) {
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                [
                    [
                        ['text' => 'link']
                    ]
                ]
            );

            //$keyboard2 = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array(array("one", "two", "three")), true); // true for one-time keyboard

            $answer = "Добро пожаловать!";
            $bot->sendMessage($message->getChat()->getId(), $answer, null, false, null, $keyboard);
        });

        $bot->run();
    }
}