<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Exception\ServiceUnavailableException;
use Cake\I18n\Time;

/**
 * MailCheck Controller
 *
 *
 * @method \App\Model\Entity\MailCheck[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MailCheckController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        return $this->redirect(['action' => 'ebon']);
    }

    /**
     * EBon Methode
     *
     * @return \Cake\Http\Response|null
     */
    public function ebon()
    {
        return $this->redirect(['controller' => 'stundenplan', 'action' => 'index']);
        if (($displayData = Cache::read('emailData', 'emailData')) === null) {
            Configure::load('app', 'default', false);
            if (file_exists(CONFIG . 'app_local.php')) {
                Configure::load('app_local', 'default');
            }

            $domain = Configure::read('EmailTransport.default.host');
            $server = "{{$domain}/imap/novalidate-cert}INBOX.Rewe E-bon";
            $adresse = Configure::read('EmailTransport.default.username');;
            $password = Configure::read('EmailTransport.default.password');;
            $mbox = imap_open($server, $adresse, $password);

            if (!$mbox) {
                throw new ServiceUnavailableException('Es konnte keine Verbindung zum Postfach hergestellt werden!');
            }

            $emails = imap_sort($mbox, SORTDATE, true);

            $displayData = [];

            if ($emails) {
                foreach ($emails as $emailId) {
                    $structure = imap_fetchstructure($mbox, $emailId, FT_UID);
                    $overview = imap_fetch_overview($mbox, $emailId)[0];

                    if (isset($structure->parts) && count($structure->parts)) {
                        for ($i = 0; $i < count($structure->parts); $i++) {
                            $attachments[$i] = array(
                                'is_attachment' => false,
                                'filename' => '',
                                'name' => '',
                                'attachment' => '');

                            if ($structure->parts[$i]->ifdparameters) {
                                foreach ($structure->parts[$i]->dparameters as $object) {
                                    if (strtolower($object->attribute) == 'filename') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['filename'] = $object->value;
                                    }
                                }
                            }

                            if ($structure->parts[$i]->ifparameters) {
                                foreach ($structure->parts[$i]->parameters as $object) {
                                    if (strtolower($object->attribute) == 'name') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['name'] = $object->value;
                                    }
                                }
                            }

                            if ($attachments[$i]['is_attachment']) {
                                $attachments[$i]['attachment'] = imap_fetchbody($mbox, $emailId, $i + 1);
                                if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }
                        } // for($i = 0; $i < count($structure->parts); $i++)
                    } // if(isset($structure->parts) && count($structure->parts))

                    if (count($attachments) != 0) {
                        foreach ($attachments as $at) {
                            if ($at['is_attachment'] == 1) {
//                            file_put_contents($at['filename'], $at['attachment']);
                                $b64 = base64_encode($at['attachment']);
                                $displayData[] = [
                                    'subject' => $overview->subject,
                                    'b64' => $b64,
                                    'time' => new Time($overview->udate),
                                ];
                            }
                        }
                    }

                }
            }

            imap_close($mbox);

            Cache::write('emailData', $displayData, 'emailData');
        }
        $this->set(compact('displayData'));
    }
}
