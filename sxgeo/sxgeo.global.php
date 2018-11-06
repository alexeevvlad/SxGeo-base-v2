<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * SxGeo IP base autoload updated by alex.vlad
 *
 * @package sxgeo
 * @author Extension made by Andrey Matsovkin, original GeoIP Db by http://sypexgeo.net/
 * @copyright Copyright (c) 2011-2013
 * @license Distributed under BSD license.
 * Made with «Extension Template» (https://github.com/macik/cot-extension_template)
 */

defined('COT_CODE') or die('Wrong URL.');
$plug_name = 'sxgeo';

//register user IP for globals use
if ($cfg['plugin']['sxgeo']['debuglocal'] && $usr['ip']=='127.0.0.1') {
	// to test in localhost mode
	$sx_debug_local = true;
	// generaing test IP
	$sx_ip = '145.255.15.68';
	//$sx_ip = '186.139.196.83';
} else {
	$sx_ip = $usr['ip'];
}

//auto init Sx globals to use in templates
if ($cfg['plugin']['sxgeo']['autoload']) {
	$sx_autoload = true;
	require_once cot_incfile('sxgeo', 'plug');
	sx_getCity($sx_ip);
	$tt = new XTemplate(cot_tplfile('sxgeo', 'plug'));
	$tt->parse();
	$sx_infoblock = $tt->text();
}

