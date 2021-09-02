<?php
/*
RdKafka Example:
https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.examples-high-level-consumer.html

RdKafka Manually installation:
https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.installation.manual.html
*/


header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
/*============================================= KAFKA */
$conf = new RdKafka\Conf();

// Set a rebalance callback to log partition assignments (optional)
$conf->setRebalanceCb(function (RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
    switch ($err) {
        case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
            echo "Assign: ";
            var_dump($partitions);
            $kafka->assign($partitions);
            break;

         case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
             echo "Revoke: ";
             var_dump($partitions);
             $kafka->assign(NULL);
             break;

         default:
            throw new Exception($err);
    }
});

// Configure the group.id. All consumer with the same group.id will consume
// different partitions.
$gr= "group1"+ rand(1,100);
$conf->set('group.id', $gr);

// Initial list of Kafka brokers
$conf->set('metadata.broker.list', 'kafka.com');

// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'earliest': start from the beginning
$conf->set('auto.offset.reset', 'earliest');

$consumer = new RdKafka\KafkaConsumer($conf);

// Subscribe to topic 'test'
//$consumer->subscribe(['inrae2']);
$consumer->subscribe(['visualisation2']);

echo "Waiting for partition assignment... (make take some time when\n";
echo "quickly re-joining the group after leaving it.)\n";

while (true) {
    $message = $consumer->consume(120*1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            echo "event: kafka\n";
            echo "data: {$message->payload}\n\n";
            ob_flush();
            flush();
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
            break;
    }
}

?>
