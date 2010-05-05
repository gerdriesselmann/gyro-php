<p>
Der Benutzer <strong><?=$hijacker->name?></strong> (<?=$hijacker->email?>) hat sich um <?=GyroDate::local_date(time())?> in Ihren Account eingeloggt.
Eventuell haben Sie <?=$hijacker->name?> darum gebeten oder <?=$hijacker->name?> Zugriff auf Ihren Account gewährt.
</p>
<p>
Wenn Sie der Meinung sind, dass <?=$hijacker->name?> sich illegtimer Weise Zugriff auf Ihren Account verschafft hat, 
teilen Sie das bitte den Administratoren unter der E-Mail-Adresse <?=Config::get_value(Config::MAIL_ADMIN)?> mit.     
</p>
