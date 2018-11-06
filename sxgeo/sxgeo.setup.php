<?php
/* ====================
[BEGIN_COT_EXT]
Code=sxgeo
Name=SxGeo IP base v2
Category=mobile-geolocation
Description=Integration of SxGeo IP base
Version=1.0.1-2.2.4
Date=2018.11.06 updated by alex.vlad
Author=Andrey Matsovkin, sypexgeo.net/
Copyright=Copyright (c) 2011-2013, Andrey Matsovkin, http://sypexgeo.net/
Notes=Original GeoIP DB created by http://sypexgeo.net/ and distributed under BSD licence. <br />If your enjoy my plugin please consider donating to help support future developments. <b>Thanks!</b> <br /><a href="mailto:macik.spb@gmail.com">macik.spb@gmail.com</a>
Auth_guests=R1
Lock_guests=W2345A
Auth_members=RW1
Lock_members=2345
Recommends_modules=
Recommends_plugins=
Requires_modules=
Requires_plugins=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
userestapi=01:radio::0:use sypexgeo REST API
autoload=02:radio::1:Autoload mode
bulkrequests=03:radio::1:Enable `bulk requests` mode
debuglocal=04:radio::0:Use sample data for debug in localhost (127.0.0.1)
[END_COT_EXT_CONFIG]
==================== */

/**
 * SxGeo IP base plugin for Cotonti CMF
 *
 * @package sxgeo
 * @author Andrey Matsovkin
 * @copyright Copyright (c) 2011-2013
 * @license Distributed under BSD license.
 * Made with «Extension Template» (https://github.com/macik/cot-extension_template)
 *
 */

defined('COT_CODE') or die('Wrong URL.');