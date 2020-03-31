<?php

// Remove latex stuff from text, and replace with valid HTML.
function de_latex($words) {
  // Curly brackets
  $words = str_replace('{', '', $words);
  $words = str_replace('}', '', $words);

  // Opening and closing quotes
  $words = str_replace('``', '&quot;', $words);
  $words = str_replace('\'\'', '&quot;', $words);

  // Hyphens
  $words = str_replace('---', '-', $words);
  $words = str_replace('--', '-', $words);

  // Joining space
  $words = str_replace('~', ' ', $words);

  return $words;
}

?>
