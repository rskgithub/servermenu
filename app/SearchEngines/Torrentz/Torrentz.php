<?php

namespace ServerMenu\SearchEngines\Torrentz;


use ServerMenu\Receiver;
use ServerMenu\SearchEngine;

class Torrentz extends SearchEngine
{

    public function getTemplateData($searchQuery, $amount = self::DEFAULT_AMOUNT, $beginAt = 0)
    {
	    $url = 'http://www.torrentz.eu/search?f='.urlencode($searchQuery);
	    
	    $context = ['http' => ['timeout' => 5]];
		$context = stream_context_create($context);
		$raw = file_get_contents("$url/usearch/$urlenc_term/?field=seeders&sorder=desc", false, $context);
		$html = new \simple_html_dom();
		$html->load(gzdecode($raw));
		
		$results = array();

		foreach($html->find('div.results dl') as $row) 
		{
			$results[] = [
				'title' => $row->find('dt a')->plaintext;
//				'subtitle' => 
			]	
		}
	    
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
