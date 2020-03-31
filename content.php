<?php
	
	function page_sidebar($current, $page_names, $sidebar_names) {
		foreach($page_names as $page=>$name) {
			// Is there a shorter sidebar name?
			if (!$entry = $sidebar_names[$page])
				$entry = $name;
			if ($entry == '==========')
				echo '&nbsp;<br>';
			else
				echo '<a style="text-decoration:none; color:darkblue; font-family:sans-serif; font-size:150%" href="'.$page.'.php">'.strtolower($entry).'</a><br>';
		}
	}


	function page_content($page) {
		//echo 'I am the page content<br>';
		require('content/'.$page.'.php');
	}
?>
	