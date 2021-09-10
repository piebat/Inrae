# INRAE
## Source code for DKE - STS4IoT paper
<<<<<<< HEAD
<span style="color:red">
<h2>Note: Action to do before run the code</h2>
</span>
* At line 1 of main.js file, you need to put your Token for [api.mapbox.com](https://docs.mapbox.com/help/getting-started/).

* In ssdemo2.php, set your Kafka server:

    * $KafkaServer = "__KAFKA ADDRESS__";
    *  $Topic_Position = 'position_data';
    *  $Topic_Alert = 'delay_alert';

## Installation
Save the all to a web server with PHP. That's all.

## SIMULATOR
The shell script **run_producer_2.sh**  publish at regular interval simulated robot coordinate. 

USAGE: 
<span style="color:green"> 
    **sh run_producer_2.sh kafka_broker_address:port**
</span>
=======
### Note: Action to do before run the code
* At line 27 of main.js file, you need to put your Token for [api.mapbox.com](https://docs.mapbox.com/help/getting-started/).
* At lines 42 of ssdemo2.php, set your Kafka server

## Installation
Save the all to a web server with PHP. That's all.
>>>>>>> 93553671b89143c1a23846da04495809677a4f87
