<?php

namespace app\Helpers;

use app\Conn\Read;
use app\Conn\Insert;
use app\Conn\Update;

class Session {

    private $Date;
    private $Cache;
    private $Traffic;
    private $Browser;

    function __construct($Cache = null) {
        session_start();
        $this->CheckSession($Cache);
    }

    private function CheckSession($Cache = null) {
        $this->Date = date('Y-m-d');
        $this->Cache = ( (int) $Cache ? $Cache : 20 );

        if (empty($_SESSION['useronline'])):
            $this->setTraffic();
            $this->setSession();
            $this->CheckBrowser();
            $this->setUsuario();
            $this->BrowserUpdate();
        else:
            $this->TrafficUpdate();
            $this->sessionUpdate();
            $this->CheckBrowser();
            $this->UsuarioUpdate();
        endif;

        $this->Date = null;  
    }

    private function setSession() {
        $_SESSION['useronline'] = [
            "online_session" => session_id(),
            "online_startview" => date('Y-m-d H:i:s'),
            "online_endview" => date('Y-m-d H:i:s', strtotime("+{$this->Cache}minutes")),
            "online_ip" => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
            "online_url" => filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT),
            "online_agent" => filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_DEFAULT)
        ];
    }

    private function sessionUpdate() {
        $_SESSION['useronline']['online_endview'] = date('Y-m-d H:i:s', strtotime("+{$this->Cache}minutes"));
        $_SESSION['useronline']['online_url'] = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT);
    }

    private function setTraffic() {
        $this->getTraffic();
        if (!$this->Traffic):
            $ArrSiteViews = [ 'siteviews_date' => $this->Date, 'siteviews_users' => 1, 'siteviews_views' => 1, 'siteviews_pages' => 1];
            $createSiteViews = new Insert;
            $createSiteViews->insertDb('jm_siteviews', $ArrSiteViews);
        else:
            if (!$this->getCookie()):
                $ArrSiteViews = [ 'siteviews_users' => $this->Traffic['siteviews_users'] + 1, 'siteviews_views' => $this->Traffic['siteviews_views'] + 1, 'siteviews_pages' => $this->Traffic['siteviews_pages'] + 1];
            else:
                $ArrSiteViews = [ 'siteviews_views' => $this->Traffic['siteviews_views'] + 1, 'siteviews_pages' => $this->Traffic['siteviews_pages'] + 1];
            endif;

            $updateSiteViews = new Update;
            $updateSiteViews->updateDb('jm_siteviews', $ArrSiteViews, "WHERE siteviews_date = :date", "date={$this->Date}");

        endif;
    }

    private function TrafficUpdate() {
        $this->getTraffic();
        $ArrSiteViews = [ 'siteviews_pages' => $this->Traffic['siteviews_pages'] + 1];
        $updatePageViews = new Update;
        $updatePageViews->updateDb('jm_siteviews', $ArrSiteViews, "WHERE siteviews_date = :date", "date={$this->Date}");
        $this->Traffic = null;
    }

    private function getTraffic() {
        $readSiteViews = new Read;
        $readSiteViews->readDb('jm_siteviews', "WHERE siteviews_date = :date", "date={$this->Date}");
    
        if ($readSiteViews->getRowCount()):
            $this->Traffic = $readSiteViews->getResult()[0];
        endif;
    }

    private function getCookie() {
        $Cookie = filter_input(INPUT_COOKIE, 'useronline', FILTER_DEFAULT);
        setcookie("useronline", base64_encode("jmxrio"), time() + 86400);
        if (!$Cookie):
            return false;
        else:
            return true;
        endif;
    }

    private function CheckBrowser() {
        $this->Browser = $_SESSION['useronline']['online_agent'];
        if (strpos($this->Browser, 'Chrome')):
            $this->Browser = 'Chrome';
        elseif (strpos($this->Browser, 'Firefox')):
            $this->Browser = 'Firefox';
        elseif (strpos($this->Browser, 'MSIE') || strpos($this->Browser, 'Trident/')):
            $this->Browser = 'IE';
        else:
            $this->Browser = 'Outros';
        endif;
    }

    private function BrowserUpdate() {  
        $readAgent = new Read;
        $readAgent->readDb('jm_siteviews_agent', "WHERE agent_name = :agent", "agent={$this->Browser}");
        
        if (!$readAgent->getResult()):
            $ArrAgent = ['agent_name' => $this->Browser, 'agent_views' => 1];
            $createAgent = new Insert;
            $createAgent->insertDb('jm_siteviews_agent', $ArrAgent);
        else:
            $ArrAgent = ['agent_views' => $readAgent->getResult()[0]['agent_views'] + 1];
            $updateAgent = new Update;
            $updateAgent->updateDb('jm_siteviews_agent', $ArrAgent, "WHERE agent_name = :name", "name={$this->Browser}");
        endif;
    }

    private function setUsuario() { 
        $sesOnline = $_SESSION['useronline'];
        $sesOnline['agent_name'] = $this->Browser;
        $userInsert = new Insert;
        $userInsert->insertDb('jm_siteviews_online', $sesOnline);
    }

    private function UsuarioUpdate() {      
        $ArrOnlone = [
            'online_endview' => $_SESSION['useronline']['online_endview'],
            'online_url' => $_SESSION['useronline']['online_url']
        ];

        $userUpdate = new Update;
        $userUpdate->updateDb('jm_siteviews_online', $ArrOnlone, "WHERE online_session = :ses", "ses={$_SESSION['useronline']['online_session']}");

        if (!$userUpdate->getRowCount()):
            $readSes = new Read;
            $readSes->readDb('jm_siteviews_online', 'WHERE online_session = :onses', "onses={$_SESSION['useronline']['online_session']}");
            if (!$readSes->getRowCount()):
                $this->setUsuario();
            endif;
        endif;
    }
}
