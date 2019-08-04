<?php
  class block_activitygraph extends block_list {
    public function init() {
        $this->title = 'Activity Graph';
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
      $this->content->footer = '<a href= "'.$CFG->wwwroot.'/blocks/activitygraph/index.php?id='.$COURSE->id.'&user='.$USER->id.'" target="_blank"><img src="'.$CFG->wwwroot.'/blocks/activitygraph/images/logo_180.png"/></a>';
      
      /* return content */ 
      return $this->content;

    }
    
}
