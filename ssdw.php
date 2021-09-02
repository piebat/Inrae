<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
$file = fopen("data/weather.json","r");

while(! feof($file)) {
  $tmp = json_decode(fgets($file));
  $msg = json_encode($tmp);
  // $value = json_decode($tmp,true);
  // echo "data: {$value['lat']},{$value['lng']},{$value['alt']}\n\n";
  echo "event: weather\n";
  echo "data: {$msg}\n\n";
  ob_flush();
  flush();
  sleep(2);
}

function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}
?>
