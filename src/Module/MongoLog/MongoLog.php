<?php

namespace Marmotz\Eeevve\Module\MongoLog;

use Hoa\Core\Event\Bucket;
use Marmotz\WallIrc\Module\Module;
use Marmotz\WallIrc\Configuration;

class MongoLog extends Module
{
    protected $mongo;

    public function setConfiguration(Configuration $configuration)
    {
        parent::setConfiguration($configuration);

        $this->mongo = (
            new \MongoClient(
                $this->getConfiguration()->get('configuration.mongolog.server')
            )
        )->selectDB(
            $this->getConfiguration()->get('configuration.mongolog.base')
        );

        return $this;
    }

    public function getSubscribedEvents()
    {
        return array(
            'join'    => 'onJoin',
            'message' => 'onMessage',
            'mode'    => 'onMode',
            'nick'    => 'onNick',
            'notice'  => 'onNotice',
            'part'    => 'onPart',
            'quit'    => 'onQuit',
        );
    }

    public function onJoin(Bucket $bucket) {
        if (isset($bucket->getData()['from']['nick'])) {
            $this->log('join', $bucket);
        }
    }

    public function onMessage(Bucket $bucket) {
        $this->log('message', $bucket);
    }

    public function onMode(Bucket $bucket) {
        $this->log('mode', $bucket);
    }

    public function onNick(Bucket $bucket) {
        $this->log('nick', $bucket);
    }

    public function onNotice(Bucket $bucket) {
        if (isset($bucket->getData()['from']['nick'])) {
            $this->log('notice', $bucket);
        }
    }

    public function onPart(Bucket $bucket) {
        $this->log('part', $bucket);
    }

    public function onQuit(Bucket $bucket) {
        $this->log('quit', $bucket);
    }

    protected function log($type, Bucket $bucket) {
        $data = $bucket->getData();

        if (isset($data['channel'])) {
            $data['channel'] = str_replace('#', '', $data['channel']);
        }

        for ($i = 0; $i <= 5; $i++) {
            if (isset($data['from']["$i"])) {
                unset($data['from']["$i"]);
            }
        }

        $this->mongo->selectCollection('raw')
            ->save(
                array_merge(
                    array(
                        'timestamp' => time(),
                        'type'      => $type,
                    ),
                    $data
                )
            )
        ;
    }
}
