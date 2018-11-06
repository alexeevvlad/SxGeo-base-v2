<?php
/**
 * Localization file for SxGeo IP base
 * @author Andrey Matsovkin
 * @copyright Copyright (c) 2011-2013
 * @license Distributed under BSD license.
 * Made with «Extension Template» (https://github.com/macik/cot-extension_template)
*/

defined('COT_CODE') or die('Wrong URL');

$L['plu_title'] = 'SxGeo IP base v2'; // Title for stand alone

$L['info_desc'] ='Integration of SxGeo IP base'; // plugin description
if (version_compare($cfg['version'], '0.9.12') > 0) // still buggy in Siena 0.9.12
	$L['info_notes'] = 'Original GeoIP DB created by http://sypexgeo.net/ and distributed under BSD licence. <br />The author of the original version: <a href="mailto:macik.spb@gmail.com">macik.spb@gmail.com</a></a> <br />If you have questions and suggestions: <a href="http://vk.com/alex.vlad">alex.vlad</a>';

$L['cfg_userestapi'] =array('use sypexgeo REST API', '');
$L['cfg_autoload'] =array('Autoload mode','Enable it to use {PHP.sx_country} and {PHP.sx_city} data in your templates.');
$L['cfg_bulkrequests'] =array('Enable `bulk requests` mode','If enabled SxGeo object not deleted after function calls.');
$L['cfg_debuglocal'] =array('Use sample IP data','used only for debug in localhost (127.0.0.1)');

$L['sx_your1'] = 'Your';
$L['sx_your2'] = 'Your ';
$L['sx_na'] = 'not defined';
$L['sx_iso_code'] = 'Country code';
$L['City'] = 'City';
$L['sx_lat'] = 'Latitude';
$L['sx_lon'] = 'Longtitude';
$L['sx_city_info'] = 'City info data';
$L['sx_city_info_ext'] = 'City info extended data';
$L['sx_localmode'] = 'sample mode enabled for localhost';

$adminhelp1 = '';