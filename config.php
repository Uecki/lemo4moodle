<?php

# Database access data (Login Details for Moodle Database)

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'moodle';

# path to moodle files (variable value assigned to constant 'moodle_path')

$dir = dirname(__FILE__);
$shortenedPath = str_replace("blocks\lemo4moodle", "", $dir);
$realPath = str_replace("\\", "/", $shortenedPath);
define('moodle_path', $realPath);	
//const moodle_path = 'D:/XAMPP/htdocs/moodle';

# web url to moodle page (HWR : www.moodle.hwr-berlin.de)
const moodle_url = 'http://localhost:8080/moodle';