<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * SxGeo IP base updated by alex.vlad
 *
 * @package sxgeo
 * @author Extension madee by Andrey Matsovkin, original GeoIP Db by http://sypexgeo.net/
 * @copyright Copyright (c) 2011-2013
 * @license Distributed under BSD license.
 * Made with «Extension Template» (https://github.com/macik/cot-extension_template)
 */

defined('COT_CODE') or die('Wrong URL.');
$plug_name = 'sxgeo';
$base_path = $cfg['plugins_dir']."/$plug_name";

require_once cot_incfile('sxgeo', 'plug');
require_once cot_langfile('sxgeo', 'plug');
$tt = new XTemplate(cot_tplfile('sxgeo', 'plug'));
$sx_toolsmode = true;
$sx['my'] = sx_getCountryCode($sx_ip);
$sx['countrycode'] = sx_getCountryCode($sx_ip);
$sx['city'] = sx_getCityInfo($sx_ip);
$sx['city_txt'] = print_r($sx['city'],1);
$sx['city_ext'] = sx_getCityInfoExt($sx_ip);
$sx['city_ext_txt'] =  print_r($sx['city_ext'],1);

$tt->assign('sx',$sx);
$tt->parse();
$plugin_body .= $tt->text();