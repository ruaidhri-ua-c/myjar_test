<?php
require __DIR__ . '/vendor/autoload.php';
require_once('app/models/InterestMessage.php');

use Rory\MyJar_Test\App\Models\InterestMessage as InterestMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$server = 'impact.ccat.eu';
$port = 5672;
$username = 'myjar';
$password = 'myjar';

$connection = new AMQPStreamConnection($server, $port, $username, $password);
$channel = $connection->channel();

$callback = function ($queueMessage) {
    echo $queueMessage->body . PHP_EOL;

    $token = 'rory';
    $interestMessage = new InterestMessage($queueMessage->body, $token);

    if (!$interestMessage->isValid()) {
        echo "### Invalid message received, ignoring: " . $queueMessage->body . PHP_EOL;
        return false;
    }

    $interestMessage->calculateInterestAmount();
    $interestMessage->calculateTotalSum();

    $queueMessage->setBody(json_encode($interestMessage));

    echo $queueMessage->body . PHP_EOL;

    $deliveryChannel = $queueMessage->delivery_info['channel'];
    $deliveryChannel->basic_publish($queueMessage, '', 'solved-interest-queue');
};

$channel->basic_consume('interest-queue', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}
