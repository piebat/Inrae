from json.decoder import JSONDecoder
from json.encoder import JSONEncoder
import kafka
import json
from time import sleep

# produce json messages
producer = kafka.KafkaProducer(bootstrap_servers=['xcvb.it:9092'],value_serializer=lambda m: json.dumps(m).encode('ascii'))

i=0
# produce asynchronously
with open('data/ros_4.dat') as f:
  data = f.readlines()
#   print(data)


for p in data:
    print("sending -> " + repr(p.strip('\n')))
    # producer.send('inrae2', p.strip('\n'))
    producer.send('visualisation2', p.strip('\n'))
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