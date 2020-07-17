<?php

require('_app/Library/PHPMailer/class.phpmailer.php');

class Email {

    /** @var PHPMailer */
    private $Mail;

    /** EMAIL DATA */
    private $Data;

    /** CORPO DO E-MAIL */
    private $Assunto;
    private $Mensagem;

    /** REMETENTE */
    private $RemetenteNome;
    private $RemetenteEmail;
    private $RemetenteTel;

    /** DESTINO */
    private $DestinoNome;
    private $DestinoEmail;

    /** CONSTROLE */
    private $Error;
    private $Result;

    function __construct() {
        $this->Mail = new PHPMailer;
        $this->Mail->Host = MAILHOST;
        $this->Mail->Port = MAILPORT;
        $this->Mail->Username = MAILUSER;
        $this->Mail->Password = MAILPASS;
        $this->Mail->CharSet = 'UTF-8';
   }

    public function Enviar(array $Data) {
        $this->Data = $Data;
        $this->Data['Assunto'] = "Enviado através do site torres despachante";
        $this->Data['DestinoNome'] = NAMEMAIL;
        $this->Data['DestinoEmail'] = DESTMAIL;
        $this->Clear();

        if (in_array('', $this->Data)):
            $this->Error = ['Erro ao enviar mensagem: Para enviar esse e-mail. Preencha os campos requisitados!', JMX_ALERT];
            $this->Result = false;
        elseif (!Check::Email($this->Data['RemetenteEmail'])):
            $this->Error = ['Erro ao enviar mensagem: O e-mail que você informou não tem um formato válido. Informe seu E-mail!', JMX_ALERT];
            $this->Result = false;
        else:
            $this->setMail();
            $this->Config();
            $this->sendMail();
        endif;
    }

    /**
     * @return BOOL $Result = TRUE or FALSE
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

    private function Clear() {
        array_map('strip_tags', $this->Data);
        array_map('trim', $this->Data);
    }

    private function setMail() {
        
        $this->Assunto = $this->Data['Assunto'];
        $this->Mensagem = $this->Data['Mensagem'];
        $this->RemetenteNome = $this->Data['RemetenteNome'];
        $this->RemetenteEmail = $this->Data['RemetenteEmail'];
        $this->RemetenteTel = $this->Data['RemetenteTel'];
        $this->DestinoNome = $this->Data['DestinoNome'];
        $this->DestinoEmail = $this->Data['DestinoEmail'];
        $this->Data = null;
        $this->setMsg();
    }

    private function setMsg() {
        $this->Mensagem = "Enviado por: {$this->RemetenteNome} <br> Telefone : {$this->RemetenteTel} <br>Email : {$this->RemetenteEmail} <br> Na data : " . date("d/m/Y – H:i", time()+(-2)*3600) . "  <br> no horário de: " . date('H:i') . "<br>Assunto : {$this->Mensagem}";
    }

    private function Config() {
        //SMTP AUTH
        $this->Mail->IsSMTP();
        $this->Mail->SMTPAuth = true;
        $this->Mail->IsHTML();

        //REMETENTE E RETORNO
        $this->Mail->From = MAILUSER;
        $this->Mail->FromName = $this->RemetenteNome;
        $this->Mail->AddReplyTo($this->RemetenteEmail, $this->RemetenteNome);

        //ASSUNTO, MENSAGEM E DESTINO
        $this->Mail->Subject = $this->Assunto;
        $this->Mail->Body = $this->Mensagem;
        $this->Mail->AddAddress($this->DestinoEmail, $this->DestinoNome);
       // $this->Mail->AddAttachment($this->arquivo);
    }

    private function sendMail() {
        if ($this->Mail->Send()):
            echo '<script language="JavaScript">  swal({ title:"Obrigado, Recebemos sua mensagem e estaremos respondendo em breve!",  confirmButtonColor: "#1a3862" });

</script>';
            $this->Result = true;
        else:
            $this->Error = ["Erro ao enviar: Entre em contato com o admin. ( {$this->Mail->ErrorInfo} )", JMX_ERROR];
            $this->Result = false;
        endif;
    }
}
