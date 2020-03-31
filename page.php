<html>

<?php
	require_once('headers.php');
	require_once('content.php');

	// Information about the pages.
	$page_names = array(
		'index' => 'Bill Smart',

		'==========',

		'papers' => 'Publications',
		'software' => 'Software',

		'==========',

		'students' => 'Students',
		'group' => 'Research Group',
		'press' => 'Press Coverage',

		'==========',

		'teaching' => 'Teaching',

		'==========',

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
	html_header($page_title);

	// Page content
	echo '<body>';
	page_header($page_names[$page]);
	echo '<p><table><tr><td valign="top">';
	page_sidebar($page, $page_names, $sidebar_names);
	echo '</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td valign="top">';
	page_content($page);
	echo '</td></tr></table><p>';
	page_footer($page);
	echo '</body';
?>

</html>
