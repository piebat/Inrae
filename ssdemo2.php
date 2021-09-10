<?php
/*
RdKafka Example:
https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.examples-high-level-consumer.html

RdKafka Manually installation:
https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.installation.manual.html
*/


header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

/* SET the following PARAMETERS */
$KafkaServer = "<SET the KAFKA SERVER>";
$Topic_Position = 'position_data';
$Topic_Alert = 'delay_alert';
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
<<<<<<< HEAD
$conf->set('metadata.broker.list', $KafkaServer);
=======
$conf->set('metadata.broker.list', 'kafka.com');
>>>>>>> 93553671b89143c1a23846da04495809677a4f87

// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'earliest': start from the beginning
$conf->set('auto.offset.reset', 'earliest');

$consumer = new RdKafka\KafkaConsumer($conf);
$consumer_alert = new RdKafka\KafkaConsumer($conf);

$consumer_alert->subscribe([$Topic_Position,$Topic_Alert]);

echo "Waiting for partition assignment... (make take some time when\n";
echo "quickly re-joining the group after leaving it.)\n";

while (true) {
    // $message = $consumer->consume(120*1000);
    $message2 = $consumer_alert->consume(120*1000);
    // switch ($message->err) {
    //     case RD_KAFKA_RESP_ERR_NO_ERROR:
    //         echo "event: kafka\n";
    //         echo "data: {$message->payload}\n\n";
    //         ob_flush();
    //         flush();
    //         break;
    //     case RD_KAFKA_RESP_ERR__PARTITION_EOF:
    //         echo "No more messages; will wait for more\n";
    //         break;
    //     case RD_KAFKA_RESP_ERR__TIMED_OUT:
    //         echo "Timed out\n";
    //         break;
    //     default:
    //         throw new \Exception($message->errstr(), $message->err);
    //         break;
    // }
    switch ($message2->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            switch ($message2->topic_name) {
                case $Topic_Position:
                    echo "event: kafka\n";
                    break;
                case $Topic_Alert:
                    echo "event: kafka_alert\n";
                    break;
            }
            echo "data: {$message2->payload}\n\n ";
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
            throw new \Exception($message2->errstr(), $message2->err);
            break;
    }

}

?>
