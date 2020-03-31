<?php

require_once('database.php');
require_once('people.php');


function student_image($person) {
  $filename = 'images/'.str_replace(' ', '', name($person)).'.jpg';

  if (file_exists($filename))
    return $filename;
  else
    return 'library/images/unknown.png';
}

function list_students($type, $header, $status='active') {
  global $database;

  $result = $database->query('select * from wds_people,wds_students where wds_people.id = wds_students.id and wds_students.type = "'.$type.'" and wds_students.status = "'.$status.'" order by last_name');

  if (sizeof($result) > 0) {
    echo '<h2>'.$header.'</h2>';

    foreach ($result as $person) {
      $p = new Student($person);
      echo $p->name();
      
      // Any publications?
      $papers = $database->query('select * from wds_paper_authors where author_id = '.$person['id']);
      if (sizeof($papers) > 0)
      	echo ' <a href="papers.php?display=author&author='.$person['id'].'">[publications]</a><br>';
      else
      	echo '<br>';

      echo '</span></span>';
    }
  }
}

function list_alumni($type, $header) {
  list_students($type, $header, 'graduated');
}

function list_students_by_type() {
  $types = array('postdoc' => 'Post-Doctoral Researchers',
		 'phd' => 'Ph.D. Students',
		 'masters' => 'Masters Students',
		 'undergrad' => 'Undergraduate Students',
		 'highschool' => 'High School Students');

  foreach($types as $tag => $header) {
    echo '<a name="'.$tag.'"></a>';
    list_students($tag, $header);
  }
}

function list_alumni_by_type() {
  $types = array('postdoc' => 'Former Post-Doctoral Researchers',
		 'phd' => 'Former Ph.D. Students',
		 'masters' => 'Former Masters Students',
		 'undergrad' => 'Former Undergraduate Students');

  echo '<a name="alumni"></a>';
  foreach($types as $tag => $header)
    list_alumni($tag, $header);
}

class Student extends Person {
  public function __construct($student) {
    parent::__construct($student);
    $data = $student;
  }
}
?>
