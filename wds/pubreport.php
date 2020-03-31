<?php

require_once('database.php');
require_once('papers.php');


function show_publication_report() {
  echo '<h2>CV Bibtex Files</h2>';
  echo '<a href="downloads/citations.tex">citations.tex</a><br>';
  echo '<a href="downloads/cv.bib">cv.bib</a><p>';

  show_publication_counts();
  show_missing_papers();
  show_cv_bibtex();
}

function show_cv_bibtex() {
  $venues = array('book' => 'B',
		  'journal' => 'J',
		  'conference' => 'C',
		  'workshop' => 'W',
		  'chapter' => 'Ch',
		  'techreport' => 'TR',
		  'editor' => 'E',
		  'patent' => 'P',
		  'thesis' => 'T',
		  'abstract' => 'A',
		  'unref' => 'O',
		  'other' => 'O');

  $cites = fopen('downloads/citations.tex', 'w');
  $entries = fopen('downloads/cv.bib', 'w');

  foreach($venues as $type => $name) {
    $result = mysql_query('select * from wds_papers where venue = "'.$type.'" order by year desc');
    $number = mysql_num_rows($result);

    if ($number > 0) {
      ob_start();
     for($i = 0; $i < $number; $i++) {
        $paper = mysql_fetch_array($result);
        display_bibtex_entry($paper);
	fwrite($cites, '\nocite'.$name.'{'.$paper['tag'].'}'.PHP_EOL);
      }
      $bibtex = tidy_bibtex(ob_get_flush());
      fwrite($entries, $bibtex);
    }
  }

  fclose($cites);
  chmod('downloads/citations.tex', 0644);

  fclose($entries);
  chmod('downloads/cv.bib', 0644);
}

function tidy_bibtex($string) {
  $subs = array('<div style="font-family: monospace; background-color: rgb(240, 240, 240)">' => '',
                '</div>' => PHP_EOL.PHP_EOL,
                '&nbsp;' => ' ',
  	        '<br>' => PHP_EOL);

  foreach($subs as $from => $to) {
    $string = str_replace($from, $to, $string);
  }

  return $string;
}

function show_publication_counts() {
  show_total_publications();
  show_publication_counts_by_type();
  show_publication_counts_by_year();
}

function show_total_publications() {
  $result = mysql_query('select * from wds_papers');
  echo mysql_num_rows($result).' total publications<br>';
}

function show_publication_counts_by_type() {
  echo '<h2>Publications by year</h2>';

  $venues = array('book' => 'Books',
		  'journal' => 'Journal Articles',
		  'conference' => 'Refereed Conference Papers',
		  'workshop' => 'Workshop Papers',
		  'chapter' => 'Book Chapters',
		  'techreport' => 'Technical Reports',
		  'editor' => 'Edited Volumes',
		  'patent' => 'Patents',
		  'thesis' => 'Theses',
		  'abstract' => 'Abstracts',
		  'unref' => 'Unrefereed Papers',
		  'other' => 'Other Publications');

  foreach($venues as $type => $name) {
    $result = mysql_query('select * from wds_papers where venue = "'.$type.'"');
    echo $name.': '.mysql_num_rows($result).'<br>';
  }
}

function show_publication_counts_by_year($earliest_year = 1990) {
  echo '<h2>Publications by year</h2>';

  for($year = date('Y'); $year >= $earliest_year; $year--) {
    $result = mysql_query('select * from wds_papers where year = '.$year);
    echo $year.': '.mysql_num_rows($result).'<br>';
  }
}

function show_missing_papers() {
  show_missing_papers_by_year();
  show_missing_papers_by_type();
}

function show_missing_papers_by_year($earliest_year = 1990) {
  echo '<h2>Missing Papers by Year</h2>';

  for($year = date('Y'); $year >= $earliest_year; $year--) {
    $result = mysql_query('select * from wds_papers where year = '.$year);
    display_missing_papers($result, $year);
  }
}

function show_missing_papers_by_type() {
  $venues = array('journal' => 'Journal Articles',
		  'conference' => 'Refereed Conference Papers',
		  'workshop' => 'Workshop Papers',
		  'chapter' => 'Book Chapters',
		  'techreport' => 'Technical Reports',
		  'thesis' => 'Theses',
		  'abstract' => 'Abstracts',
		  'unref' => 'Unrefereed Papers',
		  'other' => 'Other Publications');
  echo '<h2>Missing Papers by Type</h2>';
  foreach($venues as $type => $name) {
    $result = mysql_query('select * from wds_papers where venue = "'.$type.'"');
    display_missing_papers($result, $name);
  }
}

function display_missing_papers($result, $caption) {
  $number = mysql_num_rows($result);

  if ($number > 0) {
    $no_title = true;

    for($i = 0; $i < $number; $i++) {
      $paper = mysql_fetch_array($result);
      
      if (!file_exists('library/papers/'.$paper['year'].'/'.$paper['tag'].'.pdf')) {
	if ($no_title) {
	  echo '<h3>'.$caption.'</h3>';
	  $no_title = false;
	}
	echo $paper['title'].'<br>';
      }
    }
  }    
}

?>
