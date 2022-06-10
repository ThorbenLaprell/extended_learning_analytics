- capabilities definieren und aktivieren
- fehlende Reports (inklusive previews)
- Life cycle
- Cronjobs
- Datenspeicherung der cronjobs in moodle db
- tasks exception handeling
Simulation mit 200.000.000 Daten im Homeoffice
Anbindung des Cronloggers
Moodle Data Verzeichnis 
Alles in eine Tabelle
Bigstring als json
tasks

site admin -> server -> task ->scheduled

export oder download csv
grafik exportieren plotly.js

1.	Hintergrundrecherche
2.	Erstellung eines Moodle Local Plugins
3.	Einbettung des Plugins in die Systemadministration
4.	Erstellung einer Hauptseite des Plugins zur späteren Darstellung der Reports
5.	Testversion
    a.	Implementierung Testreport
    b.	Definierung Moodle Task
    c.	Asynchroner Testlauf
6.	Reports
    a.	Use Cases
    b.	Entwurf der Reports
    c.	Speicheranforderungen der Reports generieren
    d.	Tabellenstruktur zur Speicherung der Auswertungen festlegen
    e.	Tabelle implementieren
    f.	Implementierung der Reports
    g.	Einbettung der Reports in die Hauptseite
7.	Skript zur asynchronen Ausführung bei Task Aufruf implementieren
8.	Testphase
9.	Schreiben der Arbeit
10.	(Lifecycle)
11.	(Ausgabe der Daten als PDF)

1.	General Reports
    a.	Usage Statistics
        i.	Zugriffsstatistiken nach Monat Woche oder Tag anzeigen lassen als Liniendiagramm
    b.	Weekheatmap
        i.	Darstellung der Zugriffe nach Stunden in der Woche als Heatmap
    c.	Course Usage
        i.	Kurse mit den meisten Aufrufen als Balkendiagramm
        ii.	Kurse mit wenigesten Hits/user um Kandidaten für tote Kurse zu finden
2.	Activity Reports
    a.	Activity Usage
        i.	Balkendiagramm der Activities mit den meisten Zugriffen
    b.	Dominant Activity
        i.	Tortendiagramm über alle in Kursen meist genutzten Activities
    c.	Forum Usage
        i.	Anzahl an Foren, Diskussionen und Posts
        ii.	Foren mit den meisten Posts
        iii.	Foren mit der längsten Inaktivität um Kandidaten für tote Foren zu finden

        array(6) {
  ["4-22"]=>
  object(stdClass)#112 (2) {
    ["heatpoint"]=>
    string(4) "4-22"
    ["value"]=>
    string(1) "2"
  }
  ["4-23"]=>
  object(stdClass)#113 (2) {
    ["heatpoint"]=>
    string(4) "4-23"
    ["value"]=>
    string(2) "26"
  }
  ["4-3"]=>
  object(stdClass)#114 (2) {
    ["heatpoint"]=>
    string(3) "4-3"
    ["value"]=>
    string(1) "2"
  }
  ["5-1"]=>
  object(stdClass)#115 (2) {
    ["heatpoint"]=>
    string(3) "5-1"
    ["value"]=>
    string(1) "2"
  }
  ["5-21"]=>
  object(stdClass)#116 (2) {
    ["heatpoint"]=>
    string(4) "5-21"
    ["value"]=>
    string(2) "68"
  }
  ["5-22"]=>
  object(stdClass)#117 (2) {
    ["heatpoint"]=>
    string(4) "5-22"
    ["value"]=>
    string(1) "2"
  }
}




    