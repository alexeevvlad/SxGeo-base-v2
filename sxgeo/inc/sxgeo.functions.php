<?php
/**
 * SxGeo IP base plugin API
 * @package sxgeo
 * @author Andrey Matsovkin
 * @copyright Copyright (c) 2011-2013
 * @license Distributed under BSD license.
 */

defined('COT_CODE') or die('Wrong URL');

$sx_bulkmode = $cfg['plugin']['sxgeo']['bulkrequests'];

if(!$cfg['plugin']['sxgeo']['userestapi']) include('plugins/sxgeo/classes/SxGeo.php');

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

$sx_resturl = 'http://api.sypexgeo.net/';

// bulk mode for multiple calls to GeoIP function during one session
if (!$cfg['plugin']['sxgeo']['userestapi'] && $sx_bulkmode){
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

function sx_getRestApi($ip=null, $type = '') {
	global $cfg, $sx_ip, $sx_resturl, $_SERVER;
	if (is_null($ip)) $ip = $sx_ip;
  $return = array();
  $is_bot = preg_match(
   "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i",
   $_SERVER['HTTP_USER_AGENT']
  );
  $return = (!empty($ip) && !$is_bot) ? json_decode(file_get_contents($sx_resturl . 'json/'. $ip), 1) : array();

  return (is_array($return) ? (!empty($type) && is_array($return[$type]) ? $return[$type] : $return) : array());
}

/**
 * Return country code ISO3166-1 by IP
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return string ISO country code in upper case (EN|RU|US|...)
 */
function sx_getCountryCode($ip=null){
	global $cfg, $SxCountry, $sx_bulkmode, $sx_country, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;
  if($cfg['plugin']['sxgeo']['userestapi']) {
    $iso_code = sx_getRestApi($ip, 'country');
    $sx_country['iso'] = $iso_code = $iso_code['iso'];
  } else {
  	if (!$SxCountry) $SxCountry = new SxGeo('plugins/sxgeo/data/SxGeo.dat');
  	$sx_country['iso'] = $iso_code = $SxCountry->getCountry($ip); // возвращает двухзначный ISO-код страны
  	if (!$sx_bulkmode) unset($SxCountry); // Если нужно освободить ресурсы - удаляем объект
  }
	return $iso_code;
}

/**
 * Return country name code by IP
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return string Country name in CMS lang as defined countries.*.lang.php file
 */
function sx_getCountry($ip=null){
	global $cfg, $SxCountry, $sx_bulkmode, $sx_country, $sx_ip, $cot_countries;
	if (is_null($ip)) $ip = $sx_ip;

  if($cfg['plugin']['sxgeo']['userestapi']) {
    $iso_code = sx_getRestApi($ip, 'country');
    $iso_code = $iso_code['iso'];
  	if ($iso_code) {
  		$sx_country['name'] = $country_name = $cot_countries[mb_strtolower($iso_code)];
  	}
  } else {
  	if (!$SxCountry) $SxCountry = new SxGeo('plugins/sxgeo/data/SxGeo.dat');
  	// Определяем страну c БД содержащими страны
  	$sx_country['iso'] = $iso_code = @$SxCountry->getCountry($ip); // возвращает двухзначный ISO-код страны
  	// must use @ to discard nulled result error in some cases
  	if ($iso_code) {
  		$sx_country['name'] = $country_name = $cot_countries[mb_strtolower($iso_code)];
  	}
  	if (!$sx_bulkmode) unset($SxCountry); // Если нужно освободить ресурсы - удаляем объект
  }
	if ($country_name) return $country_name; else return $iso_code;
}

/**
 * Return Country ID for further work with SxGeoIP base
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return integer Country code used in SxGeoIP base
 */
function sx_getCountryId($ip=null){
	global $cfg, $SxCountry, $sx_bulkmode, $sx_country, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;

  if($cfg['plugin']['sxgeo']['userestapi']) {
    $iso_code = sx_getRestApi($ip, 'country');
    $sx_country['id'] = $result = $iso_code['id'];
  } else {
  	if (!$SxCountry) $SxCountry = new SxGeo('plugins/sxgeo/data/SxGeo.dat'); // Режим по умолчанию, файл бд SxGeo.dat
  	$sx_country['id'] = $result = @$SxCountry->getCountryId($ip);       // возвращает номер страны
  	if (!$sx_bulkmode) unset($SxCountry);
  }
	return $result;
}

/**
 * Returns city info array for given IP
 *
 * Sample array:
 *  [city] => Array
 *      (
 *          [id] => 479561
 *          [lat] => 54.74306
 *          [lon] => 55.96779
 *          [name_ru] => Уфа
 *          [name_en] => Ufa
 *      )
 *  [country] => Array
 *      (
 *          [id] => 185
 *          [iso] => RU
 *      )
 *
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return Ambigous <string>
 *
 */
function sx_getCityInfo($ip=null){
	global $cfg, $SxCity, $sx_bulkmode, $sx_city, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;

  if($cfg['plugin']['sxgeo']['userestapi']) {
    $data = sx_getRestApi($ip);
    $sx_city['info'] = $result = array(
      'city' => array(
        'id' => $data['city']['id'],
        'lat' => $data['city']['lat'],
        'lon' => $data['city']['lon'],
        'name_ru' => $data['city']['name_ru'],
        'name_en' => $data['city']['name_en'],
      ),
      'country' => array(
        'id' => $data['country']['id'],
        'iso' => $data['country']['iso']
      )
    );
  } else {
  	if (!$SxCity) $SxCity = new SxGeo('plugins/sxgeo/data/SxGeoCity.dat'); // Режим по умолчанию, файл бд SxGeo.dat
  	$sx_city['info'] = $result = @$SxCity ->getCity($ip);     // возвращает с краткой информацией, без названия региона и временной зоны
  	if (!$sx_bulkmode) unset($SxCity); // Если нужно освободить ресурсы - удаляем объект
  }
	return $result;
}

/**
 * Returns extended city info array for given IP address
 *
 * Sample array:
 * (
 *   [city] => Array
 *        (
 *            [id] => 479561
 *            [lat] => 54.74306
 *            [lon] => 55.96779
 *            [name_ru] => Уфа
 *            [name_en] => Ufa
 *        )
 *    [region] => Array
 *        (
 *            [id] => 578853
 *            [name_ru] => Башкортостан
 *            [name_en] => Bashkortostan
 *            [iso] => RU-BA
 *        )
 *    [country] => Array
 *        (
 *            [id] => 185
 *            [iso] => RU
 *            [lat] => 60
 *            [lon] => 100
 *            [name_ru] => Россия
 *            [name_en] => Russia
 *        )
 * )
 * @param string $ip IP address in xxx.xxx.xxx.xxx notation
 * @return array Array with city info
 */
function sx_getCityInfoExt($ip=null){
	global $cfg, $SxCity, $sx_bulkmode, $sx_city, $sx_ip;
	if (is_null($ip)) $ip = $sx_ip;

  if($cfg['plugin']['sxgeo']['userestapi']) {
    $data = sx_getRestApi($ip);
    $sx_city['info'] = $result = array(
      'city' => array(
        'id' => $data['city']['id'],
        'lat' => $data['city']['lat'],
        'lon' => $data['city']['lon'],
        'name_ru' => $data['city']['name_ru'],
        'name_en' => $data['city']['name_en'],
      ),
      'region' => array(
        'id' => $data['region']['id'],
        'name_ru' => $data['region']['name_ru'],
        'name_en' => $data['region']['name_en'],
        'iso' => $data['region']['iso']
      ),
      'country' => array(
        'id' => $data['country']['id'],
        'iso' => $data['country']['iso'],
        'lat' => $data['country']['lat'],
        'lon' => $data['country']['lon'],
        'name_ru' => $data['country']['name_ru'],
        'name_en' => $data['country']['name_en'],
      )
    );
  } else {
  	if (!$SxCity) 	$SxCity = new SxGeo('plugins/sxgeo/data/SxGeoCity.dat'); // Режим по умолчанию, файл бд SxGeo.dat
  	 $sx_city['ext_info'] = $result = @$SxCity ->getCityFull($ip); // возвращает полную информацию о городе и регионе
  	if (!$sx_bulkmode) unset($SxCity);
  }
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