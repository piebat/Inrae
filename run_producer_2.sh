
echo "Cancello Topic"
/home/kafka/kafka/bin/kafka-topics.sh --delete --bootstrap-server 10.10.1.213:9092 --topic visualisation2
echo "Lancio script"
echo "[ hit CTRL+C to stop]"
python3 producer2.py
