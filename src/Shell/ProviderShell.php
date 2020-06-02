<?php
declare(strict_types=1);

namespace App\Shell;

use App\Model\Entity\User;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Http\Exception\ServiceUnavailableException;

/**
 * Provider shell command.
 */
class ProviderShell extends Shell
{
    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        Configure::load('app', 'default', false);
        if (file_exists(CONFIG . 'app_local.php')) {
            Configure::load('app_local', 'default');
        }

        $domain = Configure::read('EmailTransport.verteiler.host');
        $server = "{{$domain}/imap/novalidate-cert}INBOX";
        $adresse = Configure::read('EmailTransport.verteiler.username');
        $password = Configure::read('EmailTransport.verteiler.password');
        $mbox = imap_open($server, $adresse, $password);
        if (!$mbox) {
            throw new ServiceUnavailableException('Es konnte keine Verbindung zum Postfach hergestellt werden!');
        }

        ini_set('SMTP', $domain);

        $emails = imap_sort($mbox, SORTDATE, 0);

        if ($emails) {
            /** @var User[] $mails */
            $mails = $this->getTableLocator()->get('users')->find();
            foreach ($emails as $emailId) {

                $mtype = array('text', 'multipart', 'message', 'application', 'audio',
                    'image', 'video', 'model', 'other');
                $mailstructure = imap_fetchstructure($mbox, $emailId); //
                $mailheader = imap_fetchheader($mbox, $emailId); //
                preg_match('/From: <([^>]*)>/', $mailheader, $from);
                preg_match('/Subject: ([^\r\n]*)/', $mailheader, $subject);
                $type = $mailstructure->type;
                if ($type == 1 && $mailstructure->ifparameters == 1) {
                    $parameters = $mailstructure->parameters;
                    $attribute = $parameters[0]->attribute;
                    $value = $parameters[0]->value;
                    echo $attribute . "//" . $value . "<br />";
                }
                # prepare the mail
                //   line below (may) not (be) needed
                //   $body="\r\nThis is a multipart message in MIME format.\r\n";
                $body = imap_body($mbox, $emailId);
                $headers = "From: INF19B Verteiler <inf19b@rindula.de>\r\n";
                $headers .= "Reply-To: {$from[1]}\r\n";
                $headers .= "Date: " . date("r") . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: " . $mtype[$mailstructure->type] . '/'
                    . strtolower($mailstructure->subtype) . ";\r\n";
                if ($type == 1) { // multipart
                    $headers .= "\t boundary=\"" . $value . "\"" . "\r\n";
                }

                foreach ($mails as $mail) {
                    $m = $mail->email;
                    imap_mail($m, $subject[1], $body, $headers);
                }

                imap_mail_move($mbox, $emailId . "", 'INBOX.verteilt');
                imap_expunge($mbox);
            }
        }

        imap_close($mbox);
        return true;
    }
}
