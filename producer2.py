#!/usr/bin/python
import argparse
from json.decoder import JSONDecoder
from json.encoder import JSONEncoder
import kafka
import json
from time import sleep

parser = argparse.ArgumentParser(description='Publish simulated trajectory', 
usage=" Require the Kafka Broker address and port as first argument \n For Example: python3 producer2.py 10.10.10.1:9092")
parser.add_argument("kafka", help="broker_address:port")
#parser.print_help()

try:
    args = parser.parse_args()
except argparse.ArgumentError:
    print ("Need specify a Kafka Broker address and port as first argument of command line. For example:  10.10.10.1:9092")

# produce json messages
producer = kafka.KafkaProducer(bootstrap_servers=[args.kafka],value_serializer=lambda m: json.dumps(m).encode('ascii'))

i=0
# produce asynchronously
with open('data/ros_4.dat') as f:
  data = f.readlines()
#   print(data)


for p in data:
    print("sending -> " + repr(p.strip('\n')))
    # producer.send('inrae2', p.strip('\n'))
    producer.send('position_data', p.strip('\n'))
    sleep(0.3)

def on_send_success(record_metadata):
    print(record_metadata.topic)
    print(record_metadata.partition)
    print(record_metadata.offset)

def on_send_error(excp):
    print('I am an errback', exc_info=excp)
    # handle exception

# block until all async messages are sent
producer.flush()