FROM mylkoh/rabbitmq-alert

LABEL maintainer="sboulema@gmail.com"

ENV host rabbitmq
ENV port 5672
ENV username guest
ENV password guest
ENV vhost /
ENV queues=
ENV checkrate 300
ENV unacknowledged_queue_size 1000
ENV slack_url=
ENV slack_channel RabbitMQ
ENV slack_username "RabbitMQ alert"

CMD rabbitmq-alert \
    --host=$host \
    --port=$port \
    --username=$username \
    --password=$password \
    --vhost=$vhost \
    --queues=$queues \
    --check-rate=$checkrate \
    --unacknowledged-queue-size=$unacknowledged_queue_size \
    --slack-url=$slack_url \
    --slack-channel=$slack_channel \
    --slack-username=$slack_username