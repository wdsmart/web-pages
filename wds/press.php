<?php

require_once('database.php');


function list_press() {
  global $database;

  $result = $database->query('select * from wds_press order by publication_date desc, id');

  if (sizeof($result) > 0) {
    echo '<div class="press"><ul>';
    foreach ($result as $piece) {
      $p = new Press($piece);
      $p->display();
    }
    echo '</ul></div>';
  }
}

class Press {
  public function __construct($piece) {
    $this->data = $piece;
  }

  public function display() {
    echo '<li class="press-piece">';

    echo '<div class="info">';
    echo '<cite>'.stripslashes($this->data['title']).'</cite>. <span class="venue">'.stripslashes($this->data['venue']).', '.date("F jS, Y", strtotime($this->data['publication_date'])).'.</span>';
    echo '</div>';

    echo '<div class="details">';
    echo $this->data['summary'];

    if ($this->data['link']) {
      echo '<span class="link">';
      echo '<a target="_blank" href="'.stripslashes($this->data['link']).'">[article]</a>';
      echo '</span>';
    }
    echo '</div>';

    echo '</li>';
  }
}

?>
