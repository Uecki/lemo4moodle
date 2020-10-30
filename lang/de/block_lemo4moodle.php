<?php
// This file is part of Moodle - http:// moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is a one-line short description of the file.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http:// www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// German language file.

$string['pluginname'] = 'Lemo4Moodle';
$string['lemo4moodle'] = 'Lemo4Moodle';
$string['lemo4moodle:addinstance'] = 'Add a new Lemo4moodle block';
$string['lemo4moodle:myaddinstance'] = 'Add a new Lemo4moodle block to the My Moodle page';
$string['content'] = 'CONTENT SETTINGS';
$string['privacy:metadata'] = 'The lemo4moodle block only displays existing data for actions inside the course.';

// General language strings (index.php).
$string['lang'] = 'de';
$string['download_dialog'] = '<b>Möchten Sie nur diesen Graphen oder alle Graphen herunterladen?</b><br><br>Hinweise zur Datensicherheit:<br>Beim Öffnen der heruntergeladenen Datei werden externe, nicht zu Moodle gehörende Bibliotheken nachgeladen
und genutzt, um die heruntergeladenen Daten zu visualisieren. Sie verlassen damit den Bereich der Hochschule. Die Nutzung ist freiwillig. Der Dienst kann ohne die Einwilligung nicht bereit gestellt werden. Mit dem Klick auf eine der Download-Optionen akzeptieren Sie, dass beim Öffnen der heruntergeladenen Datei externe Dienste Zugriff auf Ihre IP-Adresse bekommen. Es handelt sich hierbei um die Dienste JQuery, MaterializeCSS, Material Icons und Plotly.';
$string['modal_title'] = 'Dateien zusammenfügen';
$string['modal_content'] = 'Wählen Sie jetzt die Dateien, von denen die Daten zusammengefügt werden sollen.
    <br> Von den ausgewählten Dateien werden dann <u>nur</u> die Linechart-Daten zusammengefügt und in der heruntergeladenen Version dargestellt.
    <br>(Halten Sie bitte die "STRG"-Taste beim Auswählen von mehreren Dateien gedrückt und klicken Sie dabei auf die gewünschten Dateien.)';
$string['logdata'] = 'Logdaten seit: ';
$string['backup'] = 'Datensicherung: ';
$string['filter'] = 'Filter';
$string['course_activity'] = 'Kursaktivität (Moodle Bericht)';
$string['update'] = 'Aktualisieren';
$string['reset'] = 'R&uuml;ckg&auml;ngig';
$string['html_download'] = 'HTML Download';
$string['dialogTitle'] = 'Auswahl';
$string['filterStart'] = 'Beginn';
$string['filterEnd'] = 'Ende';
$string['selectStart'] = 'Module auswählen:';
$string['selectAll'] = 'Alle';

// Moodle module names.
$string['mod_assign'] = 'Aufgabe';
$string['mod_assignment'] = 'Aufgabe';
$string['mod_book'] = 'Buch';
$string['mod_chat'] = 'Chat';
$string['mod_choice'] = 'Abstimmung';
$string['mod_data'] = 'Datenbank';
$string['mod_feedback'] = 'Feedback';
$string['mod_folder'] = 'Verzeichnis';
$string['mod_forum'] = 'Forum';
$string['mod_glossary'] = 'Glossar';
$string['mod_imscp'] = 'IMS-Content';
$string['mod_label'] = 'Textfeld';
$string['mod_lesson'] = 'Lektion';
$string['mod_lti'] = 'Externes Tool';
$string['mod_page'] = 'Textseite';
$string['mod_quiz'] = 'Test';
$string['mod_resource'] = 'Arbeitsmaterial';
$string['mod_scorm'] = 'Lernpaket';
$string['mod_survey'] = 'Umfrage';
$string['mod_url'] = 'Link/URL';
$string['mod_wiki'] = 'Wiki';
$string['mod_workshop'] = 'Gegenseitige Beurteilung';

// Barchart.
$string['barchart_title'] = 'Zugriffe und Nutzer pro Datei';
$string['barchart_xlabel'] = 'Name';
$string['barchart_ylabel'] = 'Zugriffe';
$string['barchart_users'] = 'Nutzer';
$string['barchart_module'] = 'Modul';

// Linechart.
$string['linechart_colDate'] = 'Datum';
$string['linechart_colAccess'] = 'Zugriffe';
$string['linechart_colOwnAccess'] = 'Eigene Zugriffe';
$string['linechart_colUser'] = 'Nutzer';
$string['linechart_colMissingData'] = 'Fehlende Daten';
$string['linechart_title'] = 'Zugriffe und Nutzer pro Tag';

// Heatmap.
$string['heatmap_title'] = 'Aktionen pro Tag pro Zeitraum';
$string['heatmap_all'] = 'ALLE';
$string['heatmap_own'] = 'EIGENE';
$string['heatmap_overall'] = 'Gesamt';
$string['heatmap_average'] = 'Durchschnitt';
$string['heatmap_monday'] = 'Montag';
$string['heatmap_tuesday'] = 'Dienstag';
$string['heatmap_wednesday'] = 'Mittwoch';
$string['heatmap_thursday'] = 'Donnerstag';
$string['heatmap_friday'] = 'Freitag';
$string['heatmap_saturday'] = 'Samstag';
$string['heatmap_sunday'] = 'Sonntag';

// Treemap.
$string['treemap_title'] = 'Treemap für die Anzahl der Klicks pro Datei. Rechtsklick, um eine Ebene nach oben zu gelangen.';
$string['treemap_clickCount'] = ' Anzahl der Klicks: ';
$string['treemap_global'] = 'Global';
$string['treemap_files'] = 'Dateien';

// View.
$string['view_checkSelection'] = 'Überprüfen Sie ihre Auswahl (Beginn < Ende).';
$string['view_dialogThis'] = 'Dieser Graph';
$string['view_dialogAll'] = 'Alle Graphen';
$string['view_file'] = 'Datei ';
$string['view_timespan'] = 'Zeitraum: ';
$string['view_noTimespan'] = 'Kein Zeitraum vorhanden.';
$string['view_modalError'] = 'Bitte mindestens zwei Dateien zum Zusammenfügen auswählen.';
