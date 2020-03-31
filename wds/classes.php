<?php

require_once('database.php');


function list_classes($school, $area, $description='') {
  global $database;

  echo '<p>';

  if ($area == false)
    $result = $database->query('select * from wds_classes where school = "'.$school.'" order by number');
  else
    $result = $database->query('select * from wds_classes where school = "'.$school.'" and area = "'.$area.'" order by number');

  if (sizeof($result) > 0) {
    echo '<h1>'.$description.'</h1>';
    foreach ($result as $class) {
      $course = new Course($class);
      $course->display();
    }
  }
}

// Todo encapsulate these data functions in the class
// Building the all_classes set is needlessly exensive, since we're doing the same query a number of times.
function teaching_report() {
  global $database;

  echo '<h1>Teaching Report</h1>';

  $class_result = $database->query('select * from wds_classes');
  echo '<h2>Courses ('.sizeof($class_result).')</h2><ol>';
  foreach ($class_result as $course)
    echo '<li>'.$course['name'].' ('.$course['school'].')</li>';
  echo '</ol>';

  $teaching_result = $database->query('select * from wds_teaching order by year desc');
  echo '<h2>Delivered Classes ('.sizeof($teaching_result).')</h2>';
  $ugrad = 0;
  $grad = 0;
  $all_classes = array();
  foreach ($teaching_result as $class) {  
    $ugrad += $class['undergrad'];
    $grad += $class['grad'];
    $all_classes[$class['number']] = sizeof($database->query('select * from wds_classes where number = "'.$class['number'].'"'));
  }
  echo 'Undergraduates: '.$ugrad.'<br>Graduate Students: '.$grad.'<p>';

  $result = $database->query('select undergrad, grad, hours, system from wds_classes, wds_teaching where wds_classes.number = wds_teaching.number');
  $ugrad_semester = 0;
  $grad_semester = 0;
  $ugrad_quarter = 0;
  $grad_quarter = 0;
  foreach ($result as $delivery) {
    if ($delivery['system'] == 'semester') {
      $ugrad_semester += $delivery['undergrad'] * $delivery['hours'];
      $grad_semester += $delivery['grad'] * $delivery['hours'];
    } else {
      $ugrad_quarter += $delivery['undergrad'] * $delivery['hours'];
      $grad_quarter += $delivery['grad'] * $delivery['hours'];      
    }
  }

  echo '<b>Semester credit hours:</b><br>&nbsp;&nbsp;Undergraduate: '.$ugrad_semester.'<br>&nbsp;&nbsp;Graduate: '.$grad_semester.'<br>';
  echo '<b>Quarter credit hours:</b><br>&nbsp;&nbsp;Undergraduate: '.$ugrad_quarter.'<br>&nbsp;&nbsp;Graduate: '.$grad_quarter.'<br>';
  echo '<b>Total quarter-equivalent credit hours:</b><br>&nbsp;&nbsp;Undergraduate: '.($ugrad_quarter + $ugrad_semester * 1.5).'<br>&nbsp;&nbsp;Graduate: '.($grad_quarter + $grad_semester * 1.5).'<br>&nbsp;&nbsp;Total: '.($ugrad_quarter + $grad_quarter + ($ugrad_semester + $grad_semester) * 1.5).'<p>';

  echo '<h2>Problems and Updates Needed</h2>';
  echo '<b>Classes with no descriptions:</b>';
  $no_classes = false;
  foreach ($all_classes as $number => $hits) {
    if ($hits == 0)
      echo ' '.$number;
    else
      $no_classes = true;
  }
  if ($no_classes)
    echo ' None';
  echo '<p>';

  echo '<b>Last class deliveries:</b> '.$teaching_result[0]['year'].'<br>';
  foreach ($teaching_result as $delivery)
    if ($delivery['year'] == $teaching_result[0]['year'])
      echo '&nbsp;&nbsp;'.$delivery['number'].': '.$delivery['semester'].'<br>';
    else
      break;
  echo '<p>';
}

class Course {
  public function __construct($class) {
    global $database;

    $this->data = $class;
    $this->deliveries = $database->query('select * from wds_teaching where number = "'.$class['number'].'" order by year desc, semester asc');
  }

  public function display() {
    echo '<h2>'.stripslashes($this->data['name']).'</h2>';
    if ($this->data['informal'])
      echo stripslashes($this->data['informal']).' ';
    $this->display_deliveries();
      
    if ($this->data['link'] != '')
      echo ' <a target="_blank" href="'.$this->data['link'].'">[web page]</a>';
    
    if ($this->data['formal'])
      echo '<p>From the course catalog:<br>'.stripslashes($this->data['formal']);
  }

  private function display_deliveries() {
    switch (sizeof($this->deliveries)) {
      case 0:
        echo 'Never taught.';
        break;
      case 1:
        echo 'Taught one time, in the '.$this->deliveries[0]['semester'].' of '.$this->deliveries[0]['year'].'.';
        break;
      default:
        echo 'Taught '.sizeof($this->deliveries).' times.  Last taught in the ';
        if ($this->deliveries[0]['year'] == $this->deliveries[1]['year'] && $this->deliveries[0]['semester'] == 'spring' && $this->deliveries[1]['semester'] == 'summer')
          echo 'summer';
        else
          echo $this->deliveries[0]['semester'];
        echo ' of '.$this->deliveries[0]['year'].'.';
        break;
    }
  }
}

?>

