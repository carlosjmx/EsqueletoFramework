<?php
//CONFIGRAÇÕES DO BANCO ####################
define('HOST', 'localhost');
define('USER', 'root');
define('PASS', '');
define('BD', '');

//SERVIDOR DE E-MAIL ################
define('MAILUSER', 'envio@email.com.br');
define('MAILPASS', '123');
define('MAILPORT', '587');
define('MAILHOST', 'www.meusite.com.br');
define('DESTMAIL', 'contato@gmail.com');
define('NAMEMAIL', 'Um título de email');

//IDENTIDADE DO SITE ################
define('SITENAME', 'Título do site ');
define('SITEDESC', 'descrição do site');

//BASE DO SITE ####################
define('BASE', 'http://localhost/EsqueletoSite/');
define('HOME', 'http://localhost/EsqueletoSite');
define('THEME', 'default');
define('INCLUDE_PATH', HOME . '/themes/' . THEME);
define('REQUIRE_PATH', 'themes/' .  THEME);

// AUTO LOAD DE CLASSES ####################
require_once('vendor/autoload.php');

// TRATAMENTO DE ERROS #####################
//CSS constantes :: Mensagens de Erro
define('JMX_ACCEPT', 'accept');
define('JMX_INFO', 'info');
define('JMX_ALERT', 'alert');
define('JMX_ERROR', 'error');

//WSErro :: Exibe erros lançados :: Front
function ErroJMX($ErrMsg, $ErrNo, $ErrDie = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? JMX_INFO : ($ErrNo == E_USER_WARNING ? JMX_ALERT : ($ErrNo == E_USER_ERROR ? JMX_ERROR : $ErrNo)));
    echo "<div class=\"box-alert {$CssClass}\">{$ErrMsg}<span class=\"ajax_close\"></span></div>";

    if ($ErrDie):
        die;
    endif;
}

function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? JMX_INFO : ($ErrNo == E_USER_WARNING ? JMX_ALERT : ($ErrNo == E_USER_ERROR ? JMX_ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class=\"ajax_close\"></span></p>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}

set_error_handler('PHPErro');
