<?php
/**
 * Invalidate all permanent logins, except for current user
 *
 * This is done for security reasons if login credentials change. It effectively removes all logins
 * form other computers then the current
 */
class InvalidatepermanentloginsUsersBaseCommand extends CommandComposite {
    protected function do_execute() {
        $ret = new Status();

        Load::models('permantentlogins');
        Load::commands('generics/massdelete');

        /* @var $user DAOUsers */
        $user = $this->get_instance();
        /* @var $current_permanent DAOPermanentlogins */
        $current_permanent = PermanentLogins::get_current();

        $conditions = array(
            new DBCondition('id_user', '=', $user->id)
        );
        if ($current_permanent) {
            $conditions[] = new DBCondition('code', '!=', $current_permanent->code);
        }

        $cmd = new MassDeleteCommand('permanentlogins', $conditions);
        $this->append($cmd);

        return $ret;
    }
}