<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/10/14
 * Time: 23:44
 */

namespace ServerMenu;


class Utility {

	/**
	 * @param $ip
	 * @param $cidr
	 *
	 * @return bool
	 *
	 * Match an IP address to a CIDR mask
	 */
	public static function cidr_match($ip, $cidr)
	{
		list($subnet, $mask) = explode('/', $cidr);

		if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param     $bytes
	 * @param int $precision
	 *
	 * @return string
	 *
	 * Converts bytes to either kilobytes, megabytes,
	 * gigabytes or terabytes
	 */
	public static function bytes2human($bytes, $precision = 2) {
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;

		if (($bytes >= 0) && ($bytes < $kilobyte)) {
			return $bytes . ' B';

		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
			return round($bytes / $kilobyte, $precision) . ' KiB/s';

		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
			return round($bytes / $megabyte, $precision) . ' MiB/s';

		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
			return round($bytes / $gigabyte, $precision) . ' GiB/s';

		} elseif ($bytes >= $terabyte) {
			return round($bytes / $terabyte, $precision) . ' TiB/s';
		} else {
			return $bytes . ' bytes/s';
		}
	}

	/**
	 * @param $timestamp
	 *
	 * @return string
	 *
	 * Converts a UNIX timestamp to relative string
	 * from: time()+600
	 * to:   '10 minutes'
	 */
	public static function time2relative($timestamp)
	{
		if(!ctype_digit($timestamp))
			$timestamp = strtotime($timestamp);

		$diff = time() - $timestamp;
		if($diff == 0)
			return '0 secs';
		elseif($diff > 0)
		{
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 60) return 'just now';
				if($diff < 120) return '1 minute ago';
				if($diff < 3600) return floor($diff / 60) . ' minutes ago';
				if($diff < 7200) return '1 hour ago';
				if($diff < 86400) return floor($diff / 3600) . ' hours ago';
			}
			if($day_diff == 1) return 'Yesterday';
			if($day_diff < 7) return $day_diff . ' days ago';
			if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
			if($day_diff < 60) return 'last month';
			return date('F Y', $timestamp);
		}
		else
		{
			$diff = abs($diff);
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
                                if($diff < 60) return '30 secs';
				if($diff < 120) return '1 mins';
				if($diff < 3600) return floor($diff / 60) . ' mins';
				if($diff < 7200) return 'an hour';
				if($diff < 86400) return floor($diff / 3600) . ' hours';
			}
			if($day_diff == 1) return '1 day';
			if($day_diff < 4) return date('l', $timestamp);
			if($day_diff < 7 + (7 - date('w'))) return '1 week';
			if(ceil($day_diff / 7) < 4) return ceil($day_diff / 7) . ' weeks';
			if(date('n', $timestamp) == date('n') + 1) return '1 month';
			return date('F Y', $timestamp);
		}
	}

}