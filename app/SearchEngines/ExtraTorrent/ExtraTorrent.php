<?php

namespace ServerMenu\SearchEngines\ExtraTorrent;


use ServerMenu\Receiver;
use ServerMenu\SearchEngine;

class ExtraTorrent extends SearchEngine
{

    public function getTemplateData($searchQuery, $amount = self::DEFAULT_AMOUNT, $beginAt = 0)
    {
        $results = [
            [
                'title' => 'test',
                'actions' => [
                    [
                        'pluginType' => 'Services',
                        'receiverType' => 'magnet',
                        'content' => 'magnet:test',
                        'glyphicon' => 'download',
                        'title' => 'Download'
                    ]
                ]
            ]
        ];

        return $results;
    }
}
