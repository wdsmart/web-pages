This page lists the classes that I teach in the <a target="_blank" href="http://robotics.oregonstate.edu">Robotics Program</a> and in the <a target="_blank" href="http://mime.oregonstate.edu">School of Mechanical, Industrial, and Manufacturing Engineering</a> at <a target="_blank" href="http://oregonstate.edu">Oregon State University.</a>
<?php

require_once('wds/classes.php');

$areas = array(
	'robotics' => 'Robotics',
	'me' => 'Mechanical Engineering',
	'cs' => 'Computer Science',
	'hc' => 'Honors College Seminars',
	'other' => 'Other Subjects'
);

teaching_report();

switch ($_GET['school']) {
	case 'all':
		echo 'It also includes the classes that I taught in my previous position in the <a target="_blank" href="http://cse.wustl.edu">Department of Computer Science and Engineering</a> at <a target="_blank" href="http://wustl.edu">Washington University in St. Louis</a>.';
		echo '<h1>Oregon State University</h1>';
		list_classes('osu', 'me');
		echo '<h1>Washington University in St. Louis</h1>';
		list_classes('wustl', 'me');
		break;

	case 'osu':
	default:
		echo 'For a full list of the classes that I\'ve taught, including in the <a target="_blank" href="http://cse.wustl.edu">Department of Computer Science and Engineering</a> at <a target="_blank" href="http://wustl.edu">Washington University in St. Louis</a>, you should <a href="teaching.php?school=all">click here</a>.';
		foreach ($areas as $area => $description) {
			echo '<h1>'.$description.'</h1>';
			list_classes('osu', $area);
		}
	}
?>
