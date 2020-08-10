<!doctype html>

<html>

<?php
	$SEPARATOR = '=====';

	// Information about the pages.
	$page_names = array(
		'index' => 'Bill Smart',

		$SEPARATOR,

		'papers' => 'Publications',
		'software' => 'Software',

		$SEPARATOR,

		'students' => 'Students',
		'group' => 'Research Group',
		'press' => 'Press Coverage',

		$SEPARATOR,

		'teaching' => 'Teaching',

		$SEPARATOR,

		'contact' => 'Contact Information'
	);

	// Shorter versions of the page names for the sidebar.
	$sidebar_names = array(
		'index' => 'home',
		'group' => 'group',
		'press' => 'press',
		'contact' => 'contact'
	);

	// Global email address.
	function email($text = 'smartw@oregonstate.edu') {
		echo '<a href="mailto:smartw@oregonstate.edu">'.$text.'</a>';
	}

	// Get the name of the page and set the title
	$page = basename($_SERVER['PHP_SELF'], '.php');
	if ($page == 'index') {
		$page_title = 'Bill Smart';
	} else {
		$page_title = 'Bill Smart: '.$page_names[$page];
	}


	// Set the HTML headers for the page
	echo '<head><title>'.$page_title.'</title><link rel="stylesheet" href="styles.css"></head>';

	// Page content
	echo '<body>';

	// Page header
	echo '<header>'.$page_names[$page].'</header>';
	echo '<div class="row">';
	// Navigation sidebar
	echo '<nav class="sidebar"><section>';
	foreach($page_names as $p => $name) {
		// Is there a shorter sidebar name?
		if (!$entry = $sidebar_names[$p])
			$entry = $name;

		if ($entry == $SEPARATOR)
			echo '</section><section>';
		else
			echo '<a href="'.$p.'">'.strtolower($entry).'</a><br>';
	}
	echo '</section></nav>';

	// Load the page content
	echo '<article class="column"><main>';
	require('content/'.$page.'.php');
	echo '</main></article>';

	echo '</div>';

	// Set the footer
	echo '<footer><address>Page written by ';
	echo email('Bill Smart');
	echo '.</address></footer>';

	// End of page content
	echo '</body>';
?>


</html>
