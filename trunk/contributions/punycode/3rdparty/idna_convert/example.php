<?php
$encoded = '';
$decoded = '';
$add = '';
header('Content-Type: text/html; charset=UTF-8');
require_once('idna_convert.class.php');
$IDN = new idna_convert();
if (isset($_REQUEST['encode'])) {
    $decoded = isset($_REQUEST['decoded']) ? stripslashes($_REQUEST['decoded']) : '';
    $encoded = $IDN->encode($decoded);
}
if (isset($_REQUEST['decode'])) {
    $encoded = isset($_REQUEST['encoded']) ? stripslashes($_REQUEST['encoded']) : '';
    $decoded = $IDN->decode($encoded);
}
if (isset($_REQUEST['lang'])) {
    if ('de' == $_REQUEST['lang'] || 'en' == $_REQUEST['lang']) $lang = $_REQUEST['lang'];
    $add .= '<input type="hidden" name="lang" value="'.$_REQUEST['lang'].'" />'."\n";
} else {
    $lang = 'en';
}
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>phlyLabs Punycode Converter</title>
<meta name="author" content="phlyLabs">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style type="text/css">
body {
    color:black;
    background:white;
    font-size:10pt;
    font-family:Verdana, Helvetica, Sans-Serif;
}

body, form {
    margin:0px;
}

form {
    display:inline;
}

input {
    font-size:8pt;
    font-family:Verdana, Helvetica, Sans-Serif;
}

#mitte {
    text-align:center;
    vertical-align:middle;
}

#round {
    background-color:rgb(230, 230, 240);
    border:1px solid black;
    text-align:center;
    vertical-align:middle;
    padding:10px;
}

.thead {
    font-size:9pt;
    font-weight:bold;
}

#copy {
    font-size:8pt;
    color:rgb(60, 60, 80);
}

#subhead {
    font-size:8pt;
}

#bla {
    font-size:8pt;
    text-align:left;
}
</style>
</head>
<body>
<table width="768" border="0" cellpadding="50" cellspacing="0">
<tr>
 <td id="mitte">
  <div id="round">
   <strong>phlyLabs` pure PHP IDNA Converter</strong><br />
   <span id="subhead">
    See <a href="http://faqs.org/rfcs/rfc3490.html" title="IDNA" target="_blank">RFC3490</a>,
    <a href="http://faqs.org/rfcs/rfc3491.html" title="Nameprep, a Stringprep profile" target="_blank">RFC3491</a>,
    <a href="http://faqs.org/rfcs/rfc3492.html" title="Punycode" target="_blank">RFC3492</a> and
    <a href="http://faqs.org/rfcs/rfc3454.html" title="Stringprep" target="_blank">RFC3454</a><br />
   </span>
   <br />
   <div id="bla"><?php if ($lang == 'de') { ?>
   Dieser Konverter erlaubt die Übersetzung von Domainnamen zwischen der Punycode- und der
   Unicode-Schreibweise.<br />
   Geben Sie einfach den Domainnamen im entsprechend bezeichneten Feld ein und klicken Sie dann auf den darunter
   liegenden Button. Sie können einfache Domainnamen, komplette URLs (wie http://jürgen-müller.de)
   oder Emailadressen eingeben.<br />
   <br />
   Stellen Sie aber sicher, dass Ihr Browser den Zeichensatz <strong>UTF-8</strong> unterstützt.<br />
   <br />
   Wenn Sie Interesse an der zugrundeliegenden PHP-Klasse haben, können Sie diese
   <a href="http://phlymail.de/de/downloads/idna/download/">hier herunterladen</a>.<br />
   <br />
   Diese Klasse wird ohne Garantie ihrer Funktionstüchtigkeit bereit gestellt. Nutzung auf eigene Gefahr.<br />
   Um sicher zu stellen, dass eine Zeichenkette korrekt umgewandelt wurde, sollten Sie diese immer zurückwandeln
   und das Ergebnis mit Ihrer ursprünglichen Eingabe vergleichen.<br />
   <br />
   Fehler und Probleme können Sie gern an <a href="mailto:team@phlymail.de">team@phlymail.de</a> senden.<br />
   <?php } else { ?>
   This converter allows you to transfer domain names between the encoded (Punycode) notation
   and the decoded (UTF-8) notation.<br />
   Just enter the domain name in the respective field and click on the button right below it to have
   it converted. Please note, that you might even enter complete domain names (like j&#xFC;rgen-m&#xFC;ller.de)
   or a email addresses.<br />
   <br />
   Make sure, that your browser is capable of the <strong>UTF-8</strong> character encoding.<br />
   <br />
   For those of you interested in the PHP source of the underlying class, you might
   <a href="http://phlymail.de/en/downloads/idna/download/">download it here</a>.<br />
   <br />
   Please be aware, that this class is provided as is and without any liability. Use at your own risk.<br />
   To ensure, that a certain string has been converted correctly, you should convert it both ways and compare the
   results.<br />
   <br />
   Please feel free to report bugs and problems to: <a href="mailto:team@phlymail.de">team@phlymail.de</a>.<br />
   <?php } ?>
   <br />
   </div>
   <table border="0" cellpadding="2" cellspacing="2" align="center">
   <tr>
    <td class="thead" align="left">Original (Unicode)</td>
    <td class="thead" align="right">Punycode (ACE)</td>
   </tr>
   <tr>
    <td align="right">
     <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
      <input type="text" name="decoded" value="<?php echo htmlentities($decoded, null, 'UTF-8'); ?>" size="48" maxlength="255" /><br />
      <input type="submit" name="encode" value="Encode &gt;&gt;" /><?php echo $add; ?>
     </form>
    </td>
    <td align="left">
     <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
      <input type="text" name="encoded" value="<?php echo htmlentities($encoded, null, 'UTF-8'); ?>" size="48" maxlength="255" /><br />
      <input type="submit" name="decode" value="&lt;&lt; Decode" /><?php echo $add; ?>
     </form>
    </td>
   </tr>
   </table><br />
   <span id="copy">Version used: 0.5.1; (c) <a href="http://phlylabs.de">phlyLabs</a> 2004-2007</span>
</div>
 </td>
</tr>
</table>
</body>
</html>