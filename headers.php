<?php

	function html_header($name) {
		echo '<head>';

		echo '<title>'.$name.'</title>';

		// Global CSS style
		echo '<style>';
		echo 'a:link {text-decoration: none;}';
		echo 'h1 {color: slategrey; font-family: sans-serif;}';
		echo 'h2 {color: slategrey; font-family: sans-serif;}';
		echo 'h3 {color: slategrey; font-family: sans-serif;}';
		echo 'h4 {color: slategrey; font-family: sans-serif;}';
		echo 'h5 {color: slategrey; font-family: sans-serif;}';
		echo 'h6 {color: slategrey; font-family: sans-serif;}';
		echo '</style>';

		echo '</head>';
	}


	function page_header($name) {
		echo '<h1 style="text-align:right; color:darkblue; font-family:sans-serif">'.$name.'</h1>';
	}


	function page_footer($name) {
		echo '&nbsp;<p style="text-align:right; margin-top:10px"><font size="-2">Page written by ';
		email('Bill Smart');
		echo '</font></p>';
	}

?>