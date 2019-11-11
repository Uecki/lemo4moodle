<?php
  class block_lemo4moodle extends block_base {
    public function init() {
        $this->title = 'Lemo4Moodle';
    }

    public function get_content() {
      //if content not empty return it
      if ($this->content !== null) {
        return $this->content;
      }
      /* create new standard class for plugin */    
      $this->content = new stdClass;
      $this->content->items = array();

      /* "import" global vars -> (config.php) */
      global $CFG;
      global $COURSE;
      global $USER;

      /* add text to plugin body */ 
      $this->content->footer = '<a href= "'.$CFG->wwwroot.'/blocks/lemo4moodle/index.php?id='.$COURSE->id.'&user='.$USER->id.'" target="_blank"><img src="'.$CFG->wwwroot.'/blocks/lemo4moodle/images/logo_180.png" alt="Logo Lemo4moodle" width="100" height="100"/></a>';
      
      /* return content */ 
      return $this->content;

    }
    
}
