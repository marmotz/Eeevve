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
            'message' => 'onMessage',
        );
    }

    public function onMessage(Bucket $bucket) {
        $data = $bucket->getData();

        $this->mongo->selectCollection('message')
            ->save(
                array(
                    'timestamp' => time(),
                    'nick' => $data['from']['nick'],
                    'message' => $data['message']
                )
            )
        ;
    }
}
