<?php

// TODO
// Combine delatex and stripslashes into a single sanitize function

require_once('database.php');
require_once('bibtex.php');
require_once('people.php');


function show_display_types() {
  $types = array('year', 'type', 'author');
  $display_types = array();
  foreach($types as $type) {
    $display_types[] = '<a href="papers.php?display='.$type.'">'.$type.'</a>';
  }
  echo '<p><div style="text-align:right">Sort by: ['.join($display_types, ' | ').']</div></p>';
}

//======================================================================
// Paper listings
//======================================================================

function list_papers() {
  global $database;

  // Set the display type
  if (isset($_GET['display']))
    $display = $_GET['display'];
  else
    $display = 'year';

  // Set the author id
  if (isset($_GET['author'])) {
    $author = $_GET['author'];
    $result = $database->query('select * from wds_people where id = '.$author);
    if (sizeof($result) == 1) {
      $p = new Person($result[0]);
      echo '<h1>'.$p->name(false).'</h1>';
    }
    $list = false;
  } else {
    // This is Smart
    $author = 91;
    $list = true;
  }

  // Set the paper tag
  if (isset($_GET['tag']))
    $tag = $_GET['tag'];
  else
    $tag = '';

  // Display the papers
  switch ($display) {
  case 'year':
    list_papers_by_year($author);
    break;
  case 'type':
    list_papers_by_type($author);
    break;
  case 'author':
    list_papers_by_author($author, $list);
    break;
  case 'detail':
    display_paper_detail($tag);
    break;
  }
}

function list_papers_by_year($author, $earliest_year = 1990) {
  global $database;

  for($year = date('Y') + 1; $year >= $earliest_year; $year--) {
    $result = array_merge(
      $database->query('select * from wds_papers,wds_paper_authors where year = '.$year.' and wds_paper_authors.author_id = '.$author.' and wds_paper_authors.paper_id = wds_papers.tag and wds_papers.disposition = "published"'),
      $database->query('select * from wds_papers,wds_paper_editors where year = '.$year.' and wds_paper_editors.editor_id = '.$author.' and wds_paper_editors.paper_id = wds_papers.tag and wds_papers.disposition = "published"')
    );

    if (sizeof($result) > 0)
      echo '<h2>'.$year.'</h2>';

    foreach ($result as $publication) {
      $p = new Publication($publication);
      $p->display(false);
      echo '<p>';
    }
  }
}

function list_papers_by_type($author) {
  global $database;

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

  foreach($venues as $venue => $name) {
    if ($venue == 'editor')
      $result = $database->query('select * from wds_papers,wds_paper_editors where venue = "'.$venue.'" and wds_paper_editors.editor_id = '.$author.' and wds_paper_editors.paper_id = wds_papers.tag and wds_papers.disposition = "published" order by year desc');
    else
      $result = $database->query('select * from wds_papers,wds_paper_authors where venue = "'.$venue.'" and wds_paper_authors.author_id = '.$author.' and wds_paper_authors.paper_id = wds_papers.tag and wds_papers.disposition = "published" order by year desc');

    if (sizeof($result) > 0) {
      echo '<h2>'.$name.'</h2>';

      foreach ($result as $publication) {
      	$p = new Publication($publication);
        $p->display(false);
        echo '<p>';
      }
    }
  }
}

function list_papers_by_author($author, $list) {
  global $database;

  if ($list) {
    $result = array_merge(
      $database->query('select * from wds_paper_authors,wds_people where wds_paper_authors.author_id = wds_people.id order by wds_people.last_name'),
      $database->query('select * from wds_paper_editors,wds_people where wds_paper_editors.editor_id = wds_people.id order by wds_people.last_name')
    );

    // TODO: Set up the select to make things unique, using DISTINCT  
    $authors = array();
    foreach ($result as $p) {
      $person = new Person($p);
      $authors[$person->data['author_id']] = $person;
    }

    echo 'Click on an author for a list of publications.<p>';
    foreach ($authors as $id => $person) {
      echo '<a href="?display=author&author='.$id.'">'.$person->name(false).'</a><br>';
    }
  } else {
    $result = array_merge(
      $database->query('select * from wds_papers, wds_paper_authors where wds_paper_authors.author_id = '.$author.' and wds_papers.tag = wds_paper_authors.paper_id order by year desc'),
      $database->query('select * from wds_papers, wds_paper_editors where wds_paper_editors.editor_id = '.$author.' and wds_papers.tag = wds_paper_editors.paper_id order by year desc')
    );
    foreach ($result as $publication) {
      $p = new Publication($publication);
      $p->display(false);
      echo '<p>';
    }
  }
}

function display_paper_detail($tag) {
  global $database;

  $result = $database->query('select * from wds_papers where tag ="'.$tag.'"');

  if (sizeof($result) != 1) {
    echo 'Could not find a paper with the tag '.$tag;    
  } else {
    $p = new Publication($result[0]);
    $p->display(true);
  }
}

// TODO:Need to actually implement this
function delatex($text) {
  return $text;
}


class Publication {
  private $authors = array();
  private $editors = array();

  public function __construct($result) {
    global $database;

    $this->data = $result;

    // Get the publication authors
    $result = $database->query('select * from wds_paper_authors, wds_people where wds_paper_authors.paper_id = "'.$this->data['tag'].'" and wds_paper_authors.author_id = wds_people.id order by author_number');
    foreach ($result as $person)
      $this->authors[] = new Person($person);

    // Get the publication editors
    $result = $database->query('select * from wds_paper_editors, wds_people where wds_paper_editors.paper_id = "'.$this->data['tag'].'" and wds_paper_editors.editor_id = wds_people.id order by editor_number');
    foreach ($result as $person)
      $this->editors[] = new Person($person);
  }

  public function display($detail = true) {
    if ($detail) {
      echo '<h1>'.$this->title().'</h1>';
      if (sizeof($this->authors) > 0)
        echo $this->author_list().'<br>';
      if (sizeof($this->editors) > 0)
        echo $this->editor_list().' (eds)<br>';

      $this->display_bibtex_class(true);

      if ($this->data['month'])
        echo stripslashes($this->data['month']).' ';
    
      echo $this->year().'<p>';

      if ($this->data['note'])
        echo '  '.delatex(stripslashes($this->data['note']));

      if ($this->data['informal'])
        echo '<p>'.delatex(stripslashes($this->data['informal']));

      if ($this->data['abstract'])
        echo '<p>'.delatex(stripslashes($this->data['abstract']));

      echo '<p>';

      $this->display_file_links();

      echo '<p>';

      $this->display_bibtex_entry();

      echo '<p>';

      if ($this->data['link'])
        echo 'External link: <a target="_blank" href="'.stripslashes($this->data['link']).'">[link]</a><p>';

      echo '<p>';

      // TODO
      //$this->display_bibtex_entry();
    } else {
      echo '<a href="?display=detail&tag='.$this->data['tag'].'">'.$this->title().'</a><br>';
      if ($this->authors)
        echo $this->author_list().'<br>';
      elseif ($this->editors)
        echo $this->editor_list().' (eds).<br>';

      $this->display_bibtex_class(false);
      echo $this->year().'.<br>';
    }
  }

  public function display_bibtex_entry() {
    $fields = array('title', 'booktitle', 'journal', 'series', 'volume', 'number', 'chapter', 'edition', 'pages', 'publisher',
      'address', 'institution', 'organization', 'school', 'annote', 'crossref', 'howpublished', 'bibtex_key', 'type', 'note', 'month');

    echo '@'.$this->data['class'].'{'.$this->data['tag'].',<br>';

    // Authors and editors are a special case
    if (sizeof($this->authors) > 0) {
      $author_list = array();
      foreach ($this->authors as $author)
        $author_list[] = $author->bibtex_name(false);
      echo '&nbsp;&nbsp;author = {'.join($author_list, ' and ').'},<br>';
    }

    if (sizeof($this->editors) > 0) {
      $editor_list = array();
      foreach ($this->editors as $editor)
        $editor_list[] = $editor->bibtex_name(false);
     echo '&nbsp;&nbsp;editor = {'.join($editor_list, ' and ').'},<br>';
    }

    foreach($fields as $field) {
      if ($this->data[$field]) {
        echo '&nbsp;&nbsp;'.$field.' = {'.$this->data[$field].'},<br>';
      }
    }

    // Everyone has a year, so we can end with that
    echo '&nbsp;&nbsp;year = {'.$this->year().'}<br>}';
  }

  private function title() {
    return delatex($this->data['title']);
  }

  private function author_list() {
    return $this->people_list($this->authors);
  }

  private function editor_list() {
    return $this->people_list($this->editors);
  }

  private function people_list($list) {
    $number = count($list);

    switch ($number) {
    case 0:
      $retval = '';
      break;
    case 1:
      $retval = $list[0]->formal_name();
      break;
    case 2:
      $retval = $list[0]->formal_name().' and '.$list[1]->formal_name();
      break;
    default:
      $retval = '';
      for ($i = 0; $i < $number - 1; $i++)
        $retval .= $list[$i]->formal_name().', ';
      $retval .= 'and '.$list[$number - 1]->formal_name();
      break;
    }

    return $retval;
  }

  private function year() {
    switch ($this->data['disposition']) {
      case 'published':
      return stripslashes($this->data['year']);
      break;
    case 'inpress':
      return 'in press';
      break;
    case 'underreview':
      return 'under review';
      break;
    case 'draft':
      return 'draft';
      break;
    }
  }

  private function display_bibtex_class($detail) {
    switch ($this->data['class']) {
      case 'article':
      $this->display_article($detail);
      break;
    case 'book':
      $this->display_book($detail);
      break;
    case 'booklet':
      $this->display_booklet($detail);
      break;
    case 'conference':
      $this->display_conference($detail);
      break;
    case 'inbook':
      $this->display_inbook($detail);
      break;
    case 'incollection':
      $this->display_incollection($detail);
      break;
    case 'inproceedings':
      $this->display_inproceedings($detail);
      break;
    case 'manual':
      $this->display_manual($detail);
      break;
    case 'mastersthesis':
      $this->display_thesis($detail);
      break;
    case 'misc':
      $this->display_misc($detail);
      break;
    case 'phdthesis':
      $this->display_thesis($detail);
      break;
    case 'proceedings':
      $this->display_proceedings($detail);
      break;
    case 'techreport':
      $this->display_techreport($detail);
      break;
    case 'unpublished':
      $this->display_unpublished($detail);
      break;
    }
  }

  private function display_article($detail) {
    if ($this->data['journal'])
      echo delatex(stripslashes($this->data['journal']));

    if ($this->data['volume']) {
      echo ' '.delatex(stripslashes($this->data['volume']));
      if ($this->data['number']) {
        echo '('.delatex(stripslashes($this->data['number'])).')';
      }
    }
    echo ', ';

    if ($detail && ($this->data['publisher']))
      echo delatex(stripslashes($this->data['publisher'])).', ';
  }

  private function display_book($detail) {
    if ($this->data['publisher'])
      echo delatex(stripslashes($this->data['publisher'])).', ';

    if ($detail && ($this->data['address']))
      echo delatex(stripslashes($this->data['address'])).', ';
  }

  // TODO
  private function display_booklet($detail) {
    echo 'Display type not implemented';
  }

  // TODO
  private function display_conference($detail) {
    echo 'Display type not implemented';
  }

  private function display_inbook($detail) {
    if ($this->data['chapter'])
      echo 'Chapter '.stripslashes($this->data['chapter']).' ';

    if ($this->data['booktitle'])
      echo 'in &quot;'.stripslashes($this->data['booktitle']).'&quot, ';

    $editors = $this->editor_list();
    if ($editors)
      echo $editors.', ';

    if ($this->data['pages'])
      echo 'pages '.delatex(stripslashes($this->data['pages'])).', ';

    if ($this->data['publisher'])
      echo delatex(stripslashes($this->data['publisher'])).', ';
  }

  // TODO
  private function display_incollection($detail) {
    echo 'Display type not implemented';
  }

  private function display_inproceedings($detail) {
    echo 'In &quot;'.delatex(stripslashes($this->data['booktitle'])).'&quot;, ';

    $editors = $this->editor_list();
    if ($editors)
      echo $editors.', ';

    if ($this->data['volume'])
      echo 'volume '.stripslashes($this->data['volume']).', ';

    if ($this->data['number'])
      echo 'number '.stripslashes($this->data['number']).', ';

    if ($this->data['pages'])
      echo 'pages '.delatex(stripslashes($this->data['pages'])).', ';

    if ($detail && $this->data['address'])
      echo delatex(stripslashes($this->data['address'])).', ';
  }

  private function display_manual($detail) {
    if ($this->data['organization'])
      echo delatex(stripslashes($this->data['organization'])).', ';

    if ($this->data['address'])
      echo delatex(stripslashes($this->data['address'])).', ';
  }

  private function display_misc($detail) {
    if ($this->data['howpublished'])
      echo delatex(stripslashes($this->data['howpublished'])).', ';
  }

  private function display_thesis($detail) {
    if ($this->data['type'])
      echo delatex(stripslashes($this->data['type'])).', ';
    else if ($this->data['class'] == 'mastersthesis')
      echo 'Masters thesis, ';
    else if ($this->data['class'] == 'phdthesis')
      echo 'Ph.D. thesis, ';

    if ($this->data['school'])
      echo delatex(stripslashes($this->data['school'])).', ';
  
    if ($detail && $this->data['address'])
      echo delatex(stripslashes($paper['address'])).', ';
  }

  // TODO
  private function display_proceedings($detail) {
    echo 'Display type not implemented';
  }

  private function display_techreport($detail) {
    if ($this->data['type'])
      echo 'Technical report';
    else
      echo delatex(stripslashes($this->data['type']));

    if ($this->data['number'])
      echo ' '.delatex(stripslashes($this->data['number']));

    echo ', ';
  
    if ($this->data['institution']) {
      echo delatex(stripslashes($this->data['institution'])).', ';
    
      if ($detail && $this->data['address'])
        echo delatex(stripslashes($this->data['address'])).', ';
    }
  }

  // TODO
  private function display_unpublished($detail) {
    echo 'Display type not implemented';
  }

  private function display_file_links() {
    $document_types = array(
      'Paper' => array(
        'PDF' => '.pdf', 
        'Postscript' => '.ps',
        'OpenDocument' => '.odt',
        'Word' => '.doc',
        'Word XML' => '.docx'
      ),
      'Slides' => array(
        'PDF' => '-slides.pdf',
        'Postscript' => '-slides.ps',
        'OpenDocument' => '-slides-.odp',
        'Powerpoint' => '-slides.ppt',
        'Powerpoint XML' => '-slides.pptx'
      ),
      'Poster' => array(
        'PDF' => '-poster.pdf',
        'Postscript' => '-poster.ps',
        'OpenDocument' => '-poster.odp',
        'Powerpoint' => '-poster.ppt',
        'Powerpoint XML' => '-poster.pptx',
        'Word' => '-poster.doc',
        'Word XML' => '-poster.docx'
      ),
      'Data' => array(
        'TAR' => '.tar',
        'tgz' => 'Gzipped TAR',
        'zip' => '.zip'
      )
    );

    foreach ($document_types as $type => $extensions) {
      $links = '';
      foreach ($extensions as $label => $ext)
        if (file_exists('library/papers/'.$this->data['year'].'/'.$this->data['tag'].$ext))
          $links .= '<a href="library/papers/'.$this->data['year'].'/'.$this->data['tag'].$ext.'">['.$label.']</a> ';

      if ($links)
        echo $type.': '.$links.'<br>';
    }
  }
}


?>
