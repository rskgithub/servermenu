<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/10/14
 * Time: 23:44
 */

namespace ServerMenu;


/**
 * Class Utility
 *
 * Various static utility methods used throughout the application.
 *
 * @package ServerMenu
 */
class Utility
{
	
	const CACHE_DIR = __DIR__ . '/../cache';
	
	static $redis = null;
	
	public static function config()
	{
		return \Slim\Slim::getInstance()->config('s');
	}
    
    /**
     * Return pre-configured SimplePie object for parsing RSS feeds
     *
     * @param $url
     * @return \SimplePie
     */
    public static function get_simplepie($url)
    {
        $pie = new \SimplePie();
        $pie->set_cache_location(__DIR__.'/../cache');
        $pie->set_feed_url($url);
        $pie->force_feed(true);
        $pie->init();
        return $pie;
    }
    
    /**
     * Match an IP address to a CIDR mask
     *
     * @param $ip
     * @param $cidr
     *
     * @return bool
     */
    public static function cidr_match($ip, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);
        
        if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Converts bytes to either kilobytes, megabytes,
     * gigabytes or terabytes
     *
     * @param     $bytes
     * @param int $precision
     *
     * @return string
     */
    public static function bytes2human($bytes, $precision = 2, $perSecond = true)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;
        
        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            $return = $bytes . ' B';
            
        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            $return = round($bytes / $kilobyte, $precision) . ' KiB';
            
        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            $return = round($bytes / $megabyte, $precision) . ' MiB';
            
        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            $return = round($bytes / $gigabyte, $precision) . ' GiB';
            
        } elseif ($bytes >= $terabyte) {
            $return = round($bytes / $terabyte, $precision) . ' TiB';
        } else {
            $return = $bytes . ' bytes';
        }
        
        if ($perSecond)
            return $return . '/s';
        return $return;
    }
    
    /**
     * Converts a UNIX timestamp to relative string
     * from: time()+600
     * to:   '10 minutes'
     *
     * @param $timestamp
     *
     * @return string
     */
    public static function time2relative($timestamp)
    {
        if (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        
        $diff = time() - $timestamp;
        if ($diff == 0) {
            return 'unknown time left';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) {
                    return 'just now';
                }
                if ($diff < 120) {
                    return '1 minute ago';
                }
                if ($diff < 3600) {
                    return floor($diff / 60) . ' minutes ago';
                }
                if ($diff < 7200) {
                    return '1 hour ago';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600) . ' hours ago';
                }
            }
            if ($day_diff == 1) {
                return 'Yesterday';
            }
            if ($day_diff < 7) {
                return $day_diff . ' days ago';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7) . ' weeks ago';
            }
            if ($day_diff < 60) {
                return 'last month';
            }
            return date('F Y', $timestamp);
        } else {
            $diff     = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 30) {
                    return '< 30 secs';
                }
                if ($diff < 60) {
                    return '< 1 min';
                }
                if ($diff < 120) {
                    return '~ 1 min';
                }
                if ($diff < 3600) {
                    return ceil(floor($diff / 60) / 5) * 5 . ' mins';
                }
                if ($diff < 7200) {
                    return '1 hour';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600) . ' hours';
                }
            }
            if ($day_diff == 1) {
                return '1 day';
            }
            if ($day_diff < 4) {
                return date('l', $timestamp);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return '1 week';
            }
            if (ceil($day_diff / 7) < 4) {
                return ceil($day_diff / 7) . ' weeks';
            }
            if (date('n', $timestamp) == date('n') + 1) {
                return '1 month';
            }
            return date('F Y', $timestamp);
        }
    }
    
    
    public static function startsWith($haystack, $needle, $case = true)
    {
        if ($case) {
            return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
        }
        return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    
    public static function endsWith($haystack, $needle, $case = true)
    {
        if ($case) {
            return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
        }
        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
    
    public static function getFreeDiskSpace()
    {
        return self::bytes2human(disk_free_space('/'), 2, false);
    }
    
    public static function emphasizeHd($string)
    {
	    return preg_replace('/([0-9]+p)/', '<b class="hd badge badge-${1}">${1}</b>', $string);
    }
    
    public static function emphasizeToday($string)
    {
	    return preg_replace('/today/', '<b class="badge-today">${1}</b>', $string);
    }
    
    public static function cacheGet($url, $cacheMinutes = 15)
    {
	    if (is_null(self::$redis)) {
		    self::$redis = new \Redis;
		    self::$redis->connect(self::config()['app']['redis']['host']);
		    self::$redis->select(self::config()['app']['redis']['db']);
	    }
	    
	    $key = "URL:$cacheMinutes:$url";
	    $result = self::$redis->get($key);
	    
		if (empty($result)) {
		   	$handle = curl_init();
		   	
		   	curl_setopt_array($handle, [
				CURLOPT_URL => $url,
				CURLOPT_POST => false,
				CURLOPT_BINARYTRANSFER => false,
				CURLOPT_HEADER => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/601.7.7 (KHTML, like Gecko) Version/9.1.2 Safari/601.7.7',
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS => 3,
			]);

			$result = curl_exec($handle);
			self::$redis->setEx($key, $cacheMinutes * 60, $result);
		}
		
		return $result;
    }
    
}