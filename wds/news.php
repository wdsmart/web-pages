<?php

require_once('database.php');

function news_title($item) {
  return stripslashes($item['title']);
}

function news_description($item) {
  return stripslashes($item['description']);
}

function news_posting_date($item) {
  return date('j F, Y', strtotime($item['posted']));
}

function list_news_items($cutoff = 60) {
  $current_date = date('Y-m-d');
  $cutoff_date = date('Y-m-d', time() - $cutoff * 24 * 60 * 60);

  $result = mysql_query('select * from wds_news where posted <= "'.$current_date.'" and posted >= "'.$cutoff_date.'" order by posted desc');
  $number = mysql_num_rows($result);

  if ($number > 0) {
    echo '<h2>News</h2><dl>';

    for($i = 0; $i < $number; $i++) {
      $item = mysql_fetch_array($result);

      if ($i > 0)
	echo '<p>';
      echo '<dt><b>'.news_title($item).'</b></dt>';
      echo '<dd>'.news_description($item).' <font size="-1">['.news_posting_date($item).']</font></dd>';
    }

    echo '</dl>';
  }

  mysql_free_result($result);
}

?>
