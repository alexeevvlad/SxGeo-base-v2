<?php
/**
 * SxGeo IP base plugin API
 * @package sxgeo
 * @author Andrey Matsovkin
 * @copyright Copyright (c) 2011-2013
 * @license Distributed under BSD license.
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_langfile('sxgeo', 'plug');

$sx_bulkmode = $cfg['plugin']['sxgeo']['bulkrequests'];
include('plugins/sxgeo/classes/SxGeo.php');
if (!$cot_countries) include_once cot_langfile('countries', 'core');

/**
 * Contains data for country code and id from last sx_getCountry*() call.
 */
$sx_country = array(
	'iso' => '', // ISO 3166-1 code in appear case
	'name' => '', // Country name with Cotonti locale as defined in countries.*.lang.php file
	'id'  => 0 // Country ID used in SxGeoIP base
);

/**
 * Contains info about city from last sx_getCity*() call.
 */
$sx_city = array(
	'info' => array(),  // basic info, see sx_getCity() function for details
	'ext_info' => array() //extended info, see sx_getCityExt() function for details
);

// bulk mode for multiple calls to GeoIP function during one session
if ($sx_bulkmode){
	// one time init for whole session

	$SxCity = new SxGeo('plugins/sxgeo/data/SxGeoCity.dat', SXGEO_BATCH);
	// Первый параметр - имя файла с базой (используется бинарная БД Sypex Geo)
	// Второй параметр - режим работы:
	// SXGEO_FILE   (работа с файлом базы, режим по умолчанию);
	// SXGEO_BATCH  (пакетная обработка, увеличивает скорость при обработке множества
	//                IP за раз);
	// SXGEO_MEMORY (кэширование БД в памяти, еще увеличивает скорость пакетной обработки,
	//                но требует больше памяти, для загрузки всей базы в память).
}

/**
 * Return country code ISO3166-1 by IP
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return string ISO country code in upper case (EN|RU|US|...)
 */
function sx_getCountryCode($ip=null){
	global $SxCountry,$sx_bulkmode, $sx_country, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;
	if (!$SxCountry) $SxCountry = new SxGeo('plugins/sxgeo/data/SxGeo.dat');
	$sx_country['iso'] = $iso_code = $SxCountry->getCountry($ip); // возвращает двухзначный ISO-код страны
	if (!$sx_bulkmode) unset($SxCountry); // Если нужно освободить ресурсы - удаляем объект
	return $iso_code;
}

/**
 * Return country name code by IP
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return string Country name in CMS lang as defined countries.*.lang.php file
 */
function sx_getCountry($ip=null){
	global $SxCountry,$sx_bulkmode, $sx_country, $sx_ip, $cot_countries;
	if (is_null($ip)) $ip = $sx_ip;
	if (!$SxCountry) $SxCountry = new SxGeo('plugins/sxgeo/data/SxGeo.dat');
	// Определяем страну c БД содержащими страны
	$sx_country['iso'] = $iso_code = @$SxCountry->getCountry($ip); // возвращает двухзначный ISO-код страны
	// must use @ to discard nulled result error in some cases
	if ($iso_code) {
		$sx_country['name'] = $country_name = $cot_countries[mb_strtolower($iso_code)];
	}
	if (!$sx_bulkmode) unset($SxCountry); // Если нужно освободить ресурсы - удаляем объект
	if ($country_name) return $country_name; else return $iso_code;
}

/**
 * Return Country ID for further work with SxGeoIP base
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return integer Country code used in SxGeoIP base
 */
function sx_getCountryId($ip=null){
	global $SxCountry,$sx_bulkmode, $sx_country, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;
	if (!$SxCountry) $SxCountry = new SxGeo('plugins/sxgeo/data/SxGeo.dat'); // Режим по умолчанию, файл бд SxGeo.dat
	$sx_country['id'] = $result = @$SxCountry->getCountryId($ip);       // возвращает номер страны
	if (!$sx_bulkmode) unset($SxCountry);
	return $result;
}

/**
 * Returns city info array for given IP
 *
 * Sample array:
 * (
 *  [regid] => 9737
 *  [cc] => 185
 *  [fips] => 66
 *  [lat] => 60.076238
 *  [lon] => 30.121382
 *  [country] => RU
 *  [city] => Санкт-Петербург
 * )
 *
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return Ambigous <string>
 *
 */
function sx_getCityInfo($ip=null){
	global $SxCity,$sx_bulkmode, $sx_city, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;
	if (!$SxCity) $SxCity = new SxGeo('plugins/sxgeo/data/SxGeoCity.dat'); // Режим по умолчанию, файл бд SxGeo.dat
	$sx_city['info'] = $result = @$SxCity ->getCity($ip);     // возвращает с краткой информацией, без названия региона и временной зоны
	if (!$sx_bulkmode) unset($SxCity); // Если нужно освободить ресурсы - удаляем объект
	return $result;
}

/**
 * Returns extended city info array for given IP address
 *
 * Sample array:
 * (
 *  [regid] => 9737
 *  [cc] => 185
 *  [fips] => 66
 *  [lat] => 60.076238
 *  [lon] => 30.121382
 *  [country] => RU
 *  [city] => Санкт-Петербург
 *  [region_name] => Санкт-Петербург
 *  [timezone] =>
 * )
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return array Array with city info
 */
function sx_getCityInfoExt($ip=null){
	global $SxCity,$sx_bulkmode, $sx_city, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;
	if (!$SxCity) 	$SxCity = new SxGeo('plugins/sxgeo/data/SxGeoCity.dat'); // Режим по умолчанию, файл бд SxGeo.dat
	 $sx_city['ext_info'] = $result = @$SxCity ->getCityFull($ip); // возвращает полную информацию о городе и регионе
	if (!$sx_bulkmode) unset($SxCity);
	return $result;
}

/**
 * Returns city name by IP address
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return string City name (in russian locale) if exists in DB
 */
function sx_getCity($ip=null){
	$city_info = sx_getCityInfo($ip);
	return $city_info['city']['name_ru'];
}

