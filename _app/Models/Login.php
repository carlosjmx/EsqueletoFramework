<?php

namespace app\Models;

use app\Helpers\Check;
use app\Conn\Read;

class Login {

    private $Level;
    private $Email;
    private $passx7;
    private $Error;
    private $Result;

    /**
     * @param INT $Level = Nível mínimo para acesso
     */
    function __construct($Level) {
        $this->Level = (int) $Level;
    }

    /**
     * @param ARRAY $UserData = user [email], pass
     */
    public function ExeLogin(array $UserData) {
        $this->Email = (string) strip_tags(trim($UserData['user']));
        $this->passx7 = (string) strip_tags(trim($UserData['pass']));
        $this->setLogin();
    }

    /**
     * @return BOOL $Var = true para login e false para erro
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * @return BOLEAM $login = Retorna true ou mata a sessão e retorna false!
     */
    public function CheckLogin() {
        if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $this->Level):
            unset($_SESSION['userlogin']);
            return false;
        else:
            return true;
        endif;
    }

    private function setLogin() {

        if (!$this->Email || !$this->passx7 || !Check::Email($this->Email)):
            $this->Error = ['Informe seu E-mail e passx7 para efetuar o login!', JMX_INFO];
            $this->Result = false;
        elseif (!$this->getUser()):
            $this->Error = ['Os dados informados não são compatíveis!', JMX_ALERT];
            $this->Result = false;
        elseif ($this->Result['user_level']  <  $this->Level):
            $this->Error = ["Desculpe {$this->Result['user_name']}, você não tem permissão para acessar esta área!", JMX_ERROR];
            $this->Result = false;
        else:
            $this->Execute();
        endif;
    }

    private function getUser() {
        $this->passx7 = md5($this->passx7);
        $read = new Read;
        $read->readDb("jm_users", "WHERE user_email = :e AND user_password_x7 = :p", "e={$this->Email}&p={$this->passx7}");

        if ($read->getResult()):
            $this->Result = $read->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    //Executa o login armazenando a sessão!
    private function Execute() {
        if (!session_id()):
            session_start();
        endif;

        $_SESSION['userlogin'] = $this->Result;
        $this->Error = ["Olá {$this->Result['user_name']}, seja bem vindo(a). Aguarde redirecionamento!", JMX_ACCEPT];
        $this->Result = true;
    }
}
