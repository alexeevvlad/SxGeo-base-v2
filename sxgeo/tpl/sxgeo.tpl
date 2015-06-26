# Admin Tools TPL file for SxGeo IP base plugin
<!-- BEGIN: MAIN -->
<div class="block">
	<ul>
		<li>{PHP.L.sx_your1} {PHP.L.Ip}: {PHP.sx_ip} <!-- IF {PHP.sx_debug_local} -->({PHP.L.sx_localmode})<!-- ENDIF --></li>
		<li>{PHP.L.sx_your2} {PHP.L.Country}: <b>{PHP.sx_city.ext_info.country.name_ru}</b> {PHP.sx_city.ext_info.country.name_en}</li>
		<li>{PHP.L.sx_iso_code}: {PHP.sx.countrycode}</li>
    <li>{PHP.L.Region}: <!-- IF {PHP.sx_city.ext_info.region.name_ru} --><b>{PHP.sx_city.ext_info.region.name_ru}</b> {PHP.sx_city.ext_info.region.name_en}<!-- ELSE -->{PHP.L.sx_na}<!-- ENDIF --></li>
    <li>{PHP.L.City}: <!-- IF {PHP.sx_city.info.city.name_ru} --><b>{PHP.sx_city.info.city.name_ru}</b> {PHP.sx_city.info.city.name_en}<!-- ELSE -->{PHP.L.sx_na}<!-- ENDIF --></li>
		<li>{PHP.L.sx_lat}: {PHP.sx_city.info.city.lat}</li>
		<li>{PHP.L.sx_lon}: {PHP.sx_city.info.city.lon}</li>
		<!-- IF {PHP.sx_toolsmode} -->
    <hr>
		<li>City info array: {PHP.sx.city_txt}</li>
		<li>City extended info array: {PHP.sx.city_ext_txt}</li>
    <hr>
    {PHP.sx.my}
		<!-- ENDIF -->
	</ul>
</div>
<!-- END: MAIN -->