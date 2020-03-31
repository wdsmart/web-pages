<?php

require_once('database.php');


class Person {
  public function __construct($person) {
    $this->data = $person;
  }

  public function name($linked = true) {
    return $this->link_helper($this->data['common_name'], $linked);
  }

  public function formal_name($linked = true) {
    return $this->link_helper($this->data['first_name'].' '.$this->data['last_name'], $linked);
  }

  public function bibtex_name($linked = true) {
    return $this->link_helper($this->data['bibtex_name'], $linked);
  }

  private function link_helper($text, $linked) {
    if ($linked && $this->data['link'])
      return '<a target="_blank" href="'.$this->data['link'].'">'.$text.'</a>';
    else
      return $text;
  }
}

?>
