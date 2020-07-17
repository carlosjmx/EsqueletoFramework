<?php

namespace app\Helpers;
use app\Conn\Insert;
	
class CountAccess{

   private $dados ;
   private $page;
   private $Device;
   private $cidade;
   private $ip;
   private $listaIpRJ ;
   private $listaIpSP;
   private $separaIp;
   private $pages;
   private $browser;

   public function execute($pages){
       $this->pages = $pages;
       $this->getBrowser();
       $this->getDevice();
       $this->getIp();
       $this->registerAcess($this->pages);
   }
   
   public function getBrowser(){
        $useragent = $_SERVER['HTTP_USER_AGENT'];
         
        if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) :
                $browser_version=$matched[1];
                $browser = 'IE';
        elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)) :
                $browser_version=$matched[1];
                $browser = 'Opera';
        elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) :
                $browser_version=$matched[1];
                $browser = 'Firefox';
        elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)) :
                $browser_version=$matched[1];
                $browser = 'Chrome';
        elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) :
                $browser_version=$matched[1];
                $browser = 'Safari';
        else:
                $browser_version = 0;
                $browser= 'other';
        endif;

        $this->browser =  $browser;
   }

   public function getDevice(){

       $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
       $ipad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
       $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
       $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
       $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
       $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
       $symbian =  strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");

       if ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian == true): 
           if( $iphone ):
                $this->Device = "Celular Iphone" ;
           elseif( $android ):
                $this->Device = "Celular Android" ;
           else:
                $this->Device = "Celular" ;
           endif;
       else: 
           $this->Device = "Computador" ;
       endif;
   }

   public function getIp(){

      if (!empty($_SERVER['HTTP_CLIENT_IP'])){
          $this->ip = $_SERVER['HTTP_CLIENT_IP'];
      }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
          $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }else{ 
          $this->ip = $_SERVER['REMOTE_ADDR'];
      }
 
      $this->separaIp = explode(".", $this->ip);
      $this->ipInicial = $this->separaIp[0]; 
      $this->listaIpRJ = array("138","152","177","179","186","189","191","201","200","187", "127");
      $this->listaIpSP =  "157";
      $i = 0;

      if($this->ip == "177.19.35.149"):
          $this->cidade = "da Padaria";
      elseif(  $this->listaIpSP  == $this->ipInicial ):
          $this->cidade = "de São Paulo";
      elseif( $this->ipInicial !== "66" ):
          foreach( $this->listaIpRJ  as $iprj):
              if($this->ipInicial  ==  $iprj): 
       	          $this->cidade = "do Rio de janeiro";
              endif;
          endforeach;
      elseif( $this->ipInicial == "66" ):
          $this->cidade = "do Buscador";
      else:
          $this->cidade = "de outro estado";
      endif;
   }

   public function registerAcess($page){
       
      $this->page = $page;
      $this->data = array("pageView_disp"=>$this->Device, "pageView_page"=> $this->page, "pageView_browser"=>$this->browser, "pageView_ip"=>$this->ip ,  "pageView_date"=>date('Y-m-d '), "pageView_hora"=>date(' H:i', time()+(-3)*3600),"pageView_estado"=>$this->cidade);
        
      $insere = new Insert();
      $insere->insertDb("jm_pageview", $this->data);
   }

}

?>