<?php

class TBackupsController extends AppController {

    public $helpers = array ('Html','Form');

    public $name = 'TBackupsController';

    public function beforeFilter()

      {
        parent::beforeFilter();

        //check if user has rights to do this

      }

    function index() {  

        if (!isset($api)){
            require APP . 'spacebukkitcall.php';  
        }
        
        $this->layout = 'sbv1';  

        $this->set('title_for_layout', 'Backups');
    }

    function test() {
        $this->disableCache();
        //Configure::write('debug', 0);
        $this->autoRender = false;

        require APP . 'spacebukkitcall.php';

        $args = array('Server', '*');

        $test = $api->call('backup', $args, true);

        debug($test);
    }

    function getPB() {
        if ($this->request->is('ajax')) {
            $this->disableCache();
            //Configure::write('debug', 0);
            $this->autoRender = false;

            require APP . 'spacebukkitcall.php';

            $args = array();
            $status = $api->call('isBackupRunning', $args, true);

            if ($status == false) {
                echo 'false';
            } else {
                $bInfo = $api->call('getBackupInfo', $args, true);
                echo $bInfo[3].'%';
            }
        }
    }

    function getRunning() {
            if ($this->request->is('ajax')) {
            $this->disableCache();
            //Configure::write('debug', 0);
            $this->autoRender = false;

            require APP . 'spacebukkitcall.php';

            $args = array();

            $status = $api->call('isBackupRunning', $args, true);

            $bInfo = $api->call('getBackupInfo', $args, true);

            $messageTime = round(($bInfo[2] / 1000) + 240);

            if ($status) {
                $size = round((intval($bInfo[7]) / 1048576), 2);
                $title = '<h3>'.'Backing up '.$bInfo[0].'</h3>';
                $timeRunning = '<div class="b-what">'.$bInfo[6].'/'.$bInfo[5].'</div>';
                $startTime = '<br><div class="b-in">(Started on '.date('l, dS F Y \a\t H:i)', round($bInfo[2] / 1000)).'</div>';
                $bSize = '<div class="b-when">Currently '.$size.' MB</div>';

                echo<<<END
$title
$timeRunning
$startTime
$bSize
END;
            } else if($messageTime >= time()) {
                echo '<img src="./img/win.png" />';
                echo '<h3>Backup finished!</h3>';
                echo '<div class="b-what">Backup of '.$bInfo[0].' finished '.round((240 + time() - $bInfo[2] / 1000) / 60, 0, PHP_ROUND_HALF_DOWN).' minutes ago!</div>';
            }else{
                echo '<img src="./img/info.png" />';
                echo '<h3>No backups running!</h3>'."\n".'<div class="b-what">All your backups are completed!</div>';

            }
        }
    }
}