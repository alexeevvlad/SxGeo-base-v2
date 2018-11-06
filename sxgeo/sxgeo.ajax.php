<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL.');

$sxgeopath = __DIR__ . '/data/';
// Обновление файла базы данных Sypex Geo
// Настройки

$urls = array( // Путь к скачиваемому файлу
  array(
    'filename' => 'SxGeo.dat',
    'download' => 'https://sypexgeo.net/files/SxGeoCountry.zip',
    'lastupd' => $sxgeopath . 'SxGeoCountry.upd'
  ),
  array(
    'filename' => 'SxGeoCity.dat',
    'download' => 'https://sypexgeo.net/files/SxGeoCity_utf8.zip',
    'lastupd' => $sxgeopath . 'SxGeoCity.upd'
  ),
);

define('INFO', true); // Вывод сообщений о работе, true заменить на false после установки в cron

set_time_limit(600);
header('Content-type: text/plain; charset=utf8');

chdir($sxgeopath);

foreach($urls as $url) {
  if (INFO) echo "Скачиваем архив с сервера '".$url['download']."'\n";

  $fp = fopen($sxgeopath .'SxGeoTmp.zip', 'wb');
  $ch = curl_init($url['download']);
  curl_setopt_array($ch, array(
  	CURLOPT_FILE => $fp,
  	CURLOPT_HTTPHEADER => file_exists($url['lastupd']) ? array("If-Modified-Since: " .file_get_contents($url['lastupd'])) : array(),
  ));
  if(!curl_exec($ch)) die ('Ошибка при скачивании архива');
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  fclose($fp);
  if ($code == 304) {
  	@unlink($sxgeopath . 'SxGeoTmp.zip');
  	if (INFO) echo "Архив не обновился, с момента предыдущего скачивания\n";
  } else {
    if (INFO) echo "Архив с сервера скачан\n";
    // Распаковываем архив
    $fp = fopen('zip://' . $sxgeopath . 'SxGeoTmp.zip#' . $url['filename'], 'rb');
    $fw = fopen($url['filename'], 'wb');
    if ($fp) {
      if (INFO) echo "Распаковываем архив\n";
      stream_copy_to_stream($fp, $fw);
      fclose($fp);
      fclose($fw);
      if(filesize($url['filename']) == 0) die ('Ошибка при распаковке архива');
      @unlink($sxgeopath . 'SxGeoTmp.zip');
      //rename($sxgeopath . '/' . $url['filename'], $sxgeopath . $url['filename']) or die ('Ошибка при переименовании файла');
      file_put_contents($url['lastupd'], gmdate('D, d M Y H:i:s') . ' GMT');
      if (INFO) echo "Перемещен файл в {$sxgeopath}{$url['filename']}\n";
    } else {
      echo "Не получается открыть\n";
    }
  }
  echo "\n";
}

