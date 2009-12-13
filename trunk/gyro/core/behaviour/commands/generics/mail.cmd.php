<?php
Load::commands('base/mail');

/**
 * Generic overloadable class for sending a mail
 * 
 * This class is empty, since everything is in MailBaseCommand.
 * 
 * MailBaseCommand offers several overloadedble functions, so you can customize your 
 * application's Mail Command by subclassing it, like this class does.
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */ 
class MailCommand extends MailBaseCommand {
}
