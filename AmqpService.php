<?php

include PhpAmqpLib\Channel\AMQPChannel;
include PhpAmqpLib\Connection\AMQPConnection;
include PhpAmqpLib\Message\AMQPMessage;


class AmqpService
{
    /**
     * AMQP connection
     * @var AMQP connection
     */
    private $_amqp_connection;

    /**
     * AMQP channel
     * @var AMQP channel
     */
    private $_amqp_channel;

    /**
     * AMQP service instance
     * @var AMQP service instance
     */
    public  static $amqpservice;

    private function  __construct()
    {
      $this->_amqp_connection = new AMQPConnection(
          AMQP_HOST,
          AMQP_PORT,
          AMQP_USERNAME,
          AMQP_PASSWORD
      );
      $this->_amqp_channel = $this->_amqp_connection->channel();
    }
    
    public function __clone() {
    throw new Exception("Can't clone a singleton");
    }

    /**
     * Sets the instances if it is not already set.
     * @return  AMQP service instance
     */
    public static function getInstance()
    {
        if(!isset(self::$amqpservice)){
            self::$amqpservice = new AmqpService();
        }
        return self::$amqpservice;
    }

    /**
     * Sets AMQP message.
     * @param string $exchange
     * @param string $data
     */
    public function sendMessage($exchange, $data)
    {
        $msg = new AMQPMessage($data);
        $this->send($exchange, $msg);
    }

    /**
     * Send a AMQP message.
     * @param string $exchange
     * @param string $message
     */
    public function send($exchange, $message)
    {
      $this->_amqp_channel->exchange_declare(
            $exchange,
            'fanout',
            false,
            true,
            false
        );
        $this->_amqp_channel->basic_publish(
            $message,
            $exchange
        );
    }

    /**
     * Close the channel and connection.
     */
    public function close()
    {
        if(isset($this->_amqp_channel)) {
            $this->_amqp_channel->close();
        }
        if(isset($this->_amqp_connection)) {
            $this->_amqp_connection->close();
        }
    }
}
