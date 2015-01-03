<?php

namespace Marmotz\Eeevve;

use DateTime;
use Marmotz\WallIrc\Configuration;
use Marmotz\WallIrc\ConfigurationLoader\File as ConfigurationFile;

class WebApp
{
    protected $channel;
    protected $date;

    public function __construct($configFile)
    {
        // load configuration
        $this->setConfiguration(
            (new Configuration)->load(
                ConfigurationFile::load(
                    $configFile
                )
            )
        );

        // load mongo database
        $this->mongo = (
            new \MongoClient(
                $this->getConfiguration()->get('configuration.mongolog.server')
            )
        )->selectDB(
            $this->getConfiguration()->get('configuration.mongolog.base')
        );

        // load URI parameters
        list($this->channel, $this->date) = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        if (!$this->channel) {
            $this->channel = null;
        }

        if (!$this->date) {
            $this->date = null;
        } else {
            $this->date = new DateTime($this->date);
        }
    }

    public function generateUrl($channel = null, $date = null)
    {
        return sprintf(
            '/%s/%s',
            $channel !== null ? $channel : $this->channel,
            $date !== null    ? $date    : $this->getDateString()
        );
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDateString()
    {
        if ($this->date instanceof DateTime) {
            return $this->date->format('Y-m-d');
        }

        return '';
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getChannels()
    {
        return $this->mongo->selectCollection('raw')
            ->distinct('data.channel')
        ;
    }

    public function getMinTimestamp()
    {
        if ($this->getChannel()) {
            return $this->mongo->selectCollection('raw')
                ->find()
                ->sort(
                    array(
                        'timestamp' => 1
                    )
                )
                ->limit(1)
                ->getNext()['timestamp']
            ;
        }
    }

    public function getMaxTimestamp()
    {
        if ($this->getChannel()) {
            return $this->mongo->selectCollection('raw')
                ->find()
                ->sort(
                    array(
                        'timestamp' => -1
                    )
                )
                ->limit(1)
                ->getNext()['timestamp']
            ;
        }
    }

    public function getLogs()
    {
        if ($this->getChannel()) {
            $date = $this->getDate() ?: new DateTime('00:00:00');

            return $this->mongo->selectCollection('raw')
                ->find(
                    array(
                        'timestamp' => array(
                            '$gte' => $date->getTimestamp(),
                            '$lt'  => $date->modify('+1 day')->getTimestamp(),
                        ),
                        '$or' => array(
                            array('data.channel' => array('$exists' => false)),
                            array('data.channel' => $this->getChannel()),
                        ),
                    )
                )
                ->sort(
                    array(
                        'timestamp' => 1,
                    )
                )
            ;
        } else {
            return array();
        }
    }

    protected function getConfiguration()
    {
        return $this->configuration;
    }

    protected function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
