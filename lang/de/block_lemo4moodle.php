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
$string['lemo4moodle:addinstance'] = 'Füge einen neuen Lemo4Moodle Block hinzu';
$string['lemo4moodle:myaddinstance'] = 'Füge einen neuen Lemo4Moodle Block zu der My Moodle Seite hinzu';
$string['content'] = 'CONTENT SETTINGS';
$string['privacy:metadata'] = 'Der Lemo4Moodle Block zeigt nur bereits existierende Daten der im Kurs getätigten Aktionen an.';

// General language strings (index.php).
$string['lang'] = 'de';
$string['download_dialog'] = '<b>Möchten Sie nur diesen Graphen oder alle Graphen herunterladen?</b><br><br>Hinweise zur Datensicherheit:<br>Beim Öffnen der heruntergeladenen Datei werden externe, nicht zu Moodle gehörende Bibliotheken nachgeladen
und genutzt, um die heruntergeladenen Daten zu visualisieren. Sie verlassen damit den Bereich der Hochschule. Die Nutzung ist freiwillig. Der Dienst kann ohne die Einwilligung nicht bereit gestellt werden. Mit dem Klick auf eine der Download-Optionen akzeptieren Sie, dass beim Öffnen der heruntergeladenen Datei externe Dienste Zugriff auf Ihre IP-Adresse bekommen. Es handelt sich hierbei um die Dienste JQuery, MaterializeCSS, Material Icons und Plotly.';
$string['modal_title'] = 'Dateien zusammenfügen';
$string['modal_content'] = 'Wählen Sie jetzt die Dateien, von denen die Daten zusammengefügt werden sollen.<br> Sollte es eine Lücke zwischen den Zeiträumen der in den Dateien gespeicherten Daten geben, dann wird diese im Linechart-Tab der zusammengefügten Datei dargestellt.<br>(Halten Sie bitte die "STRG"-Taste beim Auswählen von mehreren Dateien gedrückt und klicken Sie dabei auf die gewünschten Dateien.)';
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
$string['selectStart'] = 'Aktivität oder Material auswählen:';
$string['selectAll'] = 'Alle';

// Moodle module names.
$string['assign'] = 'Aufgabe';
$string['assignment'] = 'Aufgabe';
$string['book'] = 'Buch';
$string['chat'] = 'Chat';
$string['choice'] = 'Abstimmung';
$string['data'] = 'Datenbank';
$string['feedback'] = 'Feedback';
$string['folder'] = 'Verzeichnis';
$string['forum'] = 'Forum';
$string['glossary'] = 'Glossar';
$string['imscp'] = 'IMS-Content';
$string['label'] = 'Textfeld';
$string['lesson'] = 'Lektion';
$string['lti'] = 'Externes Tool';
$string['page'] = 'Textseite';
$string['quiz'] = 'Test';
$string['resource'] = 'Arbeitsmaterial';
$string['scorm'] = 'Lernpaket';
$string['survey'] = 'Umfrage';
$string['url'] = 'Link/URL';
$string['wiki'] = 'Wiki';
$string['workshop'] = 'Gegenseitige Beurteilung';

// Barchart.
$string['barchart_title'] = 'Zugriffe und Nutzer pro Aktivität oder Material';
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
