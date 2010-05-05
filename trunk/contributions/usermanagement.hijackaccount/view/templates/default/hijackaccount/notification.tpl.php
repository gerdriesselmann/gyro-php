<p>
The user <strong><?=$hijacker->name?></strong> (<?=$hijacker->email?>) logged into your account at <?=GyroDate::local_date(time())?>.
This probably happened because you asked for it or you granted <?=$hijacker->name?> access to your account.
</p>
<p>
If you however feel offended by <?=$hijacker->name?> feel free to contact the administrators using the e-mail-address <?=Config::get_value(Config::MAIL_ADMIN)?>.     
</p>
