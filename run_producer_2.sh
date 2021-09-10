
echo "Cancello Topic"
/home/kafka/kafka/bin/kafka-topics.sh --delete --bootstrap-server $1 --topic position_data
echo "Lancio script"
echo "[ hit CTRL+C to stop]"
python3 producer2.py $1
