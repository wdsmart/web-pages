<?php

require_once('database.php');


function list_press() {
  global $database;

  $result = $database->query('select * from wds_press order by publication_date desc, id');

  if (sizeof($result) > 0) {
    foreach ($result as $piece) {
      $p = new Press($piece);
      $p->display();
    }
  }
}

class Press {
  public function __construct($piece) {
    $this->data = $piece;
  }

  public function display() {
    echo '<b>'.stripslashes($this->data['venue']).'</b>, '.stripslashes($this->data['publication_date']).'<br>';
    echo $this->data['summary'].'<br>';
    if ($this->data['link'])
      echo '<a target="_blank" href="'.stripslashes($this->data['link']).'">[article]</a>';
    echo '<p>';
  }
}

?>
