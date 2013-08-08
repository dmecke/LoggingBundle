<?php

namespace Cunningsoft\LoggingBundle\Services;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class PDOHandler extends AbstractProcessingHandler
{
    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $statement;

    public function __construct(\PDO $pdo, $level = Logger::DEBUG, $bubble = true)
    {
        $this->pdo = $pdo;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $this->statement->execute(array(
            'token' => $record['extra']['token'],
            'channel' => $record['channel'],
            'level' => $record['level'],
            'level_name' => $record['level_name'],
            'file' => isset($record['extra']['file']) ? $record['extra']['file'] : '',
            'line' => isset($record['extra']['line']) ? $record['extra']['line'] : '',
            'class' => isset($record['extra']['class']) ? $record['extra']['class'] : '',
            'function' => isset($record['extra']['function']) ? $record['extra']['function'] : '',
            'message' => $record['message'],
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
        ));
    }

    private function initialize()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS monolog (id INTEGER AUTO_INCREMENT PRIMARY KEY, token VARCHAR(255), channel VARCHAR(255), level INTEGER, level_name VARCHAR(255), file VARCHAR(255), line VARCHAR(255), class VARCHAR(255), function VARCHAR(255), message LONGTEXT, time DATETIME)');
        $this->statement = $this->pdo->prepare('INSERT INTO monolog (token, channel, level, level_name, file, line, class, function, message, time) VALUES (:token, :channel, :level, :level_name, :file, :line, :class, :function, :message, :time)');
        $this->initialized = true;
    }
}
