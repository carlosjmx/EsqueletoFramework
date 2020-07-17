<?php

namespace app\Helpers;

class Check {

    private static $Data;
    private static $Format;

    /**
     *
     * @param STRING $Email = Uma conta de e-mail
     * @return BOOL = True para um email válido, ou false
     */
    public static function emailFormat($Email) {
       
        self::$Data = (string) $Email;
        self::$Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';
        if (preg_match(self::$Format, self::$Data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     *
     * @param STRING $Name = Uma string qualquer
     * @return STRING = $Data = Uma URL amigável válida
     */
    public static function urlFormat($Name) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        self::$Data = strtr(utf8_decode($Name), utf8_decode(self::$Format['a']), self::$Format['b']);
        self::$Data = strip_tags(trim(self::$Data));
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('-----', '----', '---', '--'), '-', self::$Data);

        return strtolower(utf8_encode(self::$Data));
    }

    /**
    *
     * @param STRING $Name = Data em (d/m/Y) ou (d/m/Y H:i:s)
     * @return STRING = $Data = Data no formato timestamp!
     */
    public static function dateFormat($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (empty(self::$Format[1])):
            self::$Format[1] = date('H:i:s');
        endif;

        self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0] . ' ' . self::$Format[1];
        return self::$Data;
    }

    /**
     *
     * @param STRING $String = Uma string qualquer
     * @return INT = $Limite = String limitada pelo $Limite
     */
    public static function wordLimit($String, $Limite, $Pointer = null) {
        self::$Data = strip_tags(trim($String));
        self::$Format = (int) $Limite;

        $ArrWords = explode(' ', self::$Data);
        $NumWords = count($ArrWords);
        $NewWords = implode(' ', array_slice($ArrWords, 0, self::$Format));

        $Pointer = (empty($Pointer) ? '<span class="continue"> ... </span>' : ' ' . $Pointer );
        $Result = ( self::$Format < $NumWords ? $NewWords . $Pointer : self::$Data );
        return $Result;
    }

    /**
     *
     * @param STRING $category_name = URL da categoria
     * @return INT $category_id = id da categoria informada
     */
    public static function catByName($CategoryName) {
        $read = new Read;
        $read->readDb('jm_categories', "WHERE category_name = :name", "name={$CategoryName}");
        if ($read->getRowCount()):
            return $read->getResult()[0]['category_id'];
        else:
            echo "A categoria {$CategoryName} não foi encontrada!";
            die;
        endif;
    }

    public static function subCatByName($subCategoryName) {
        $read = new Read;
        $read->readDb('jm_sub_category', "WHERE sub_category_name = :name", "name={$subCategoryName}");
        if ($read->getRowCount()):
            return $read->getResult()[0]['id_sub_category'];
        else:
            echo "A Subcategoria {$subCategoryName} não foi encontrada!";
            die;
        endif;
    }

    /**
     *
     * @return INT = Qtd de usuários online
     */
    public static function UserOnline() {
        $now = date('Y-m-d H:i:s');
        $deleteUserOnline = new Delete;
        $deleteUserOnline->ExeDelete('jm_siteviews_online', "WHERE online_endview < :now", "now={$now}");

        $readUserOnline = new Read;
        $readUserOnline->readDb('jm_siteviews_online');
        return $readUserOnline->getRowCount();
    }

    /**
     *
     * @return HTML = imagem redimencionada!
     */
    public static function resizeImage($ImageUrl, $ImageDesc, $ImageW = null, $ImageH = null) {

        self::$Data = $ImageUrl;

        if (file_exists(self::$Data) && !is_dir(self::$Data)):
            $patch = HOME;
            $imagem = self::$Data;
            return "<img src=\"{$patch}/tim.php?src={$patch}/{$imagem}&w={$ImageW}&h={$ImageH}\" alt=\"{$ImageDesc}\" title=\"{$ImageDesc}\"/>";
        else:
            return false;
        endif;
    }

}
