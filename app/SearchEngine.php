<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 28/10/14
 * Time: 20.21
 */

namespace ServerMenu;


abstract class SearchEngine {

	const DEFAULT_AMOUNT = 30;

	/**
	 * Return array with search results and Service senders.
	 *
	 * @param string $searchQuery
	 * @param int $amount
	 * @param int $beginAt
	 * @return mixed
	 */
	abstract public function getTemplateData($searchQuery, $amount = self::DEFAULT_AMOUNT, $beginAt = 0);

}