<?php

namespace app\Helpers;

class Upload {

    private $File;
    private $Name;
    private $Send;

    private $ImageWidth;
    private $Image;

    private $Result;
    private $Error;

    private $Folder;
    private static $BaseDir;

    function __construct($BaseDir = null) {
        self::$BaseDir = ( (string) $BaseDir ? $BaseDir : '../uploads/');
        if (!file_exists(self::$BaseDir) && !is_dir(self::$BaseDir)):
            mkdir(self::$BaseDir, 0777);
        endif;
    }

    /**
     * @param FILES $Image = Enviar imagem $_FILES (JPG ou PNG)
     * @param STRING $Name = Nome da imagems ( ou do artigo )
     * @param INT $ImageWidth = Largura da imagem ( 1024 padrão )
     * @param STRING $Folder = Pasta personalizada
     */
    public function Image(array $Image, $Name = null, $ImageWidth = null, $Folder = null) {
        $this->File = $Image;
        $this->Name = ( (string) $Name ? $Name : substr($Image['name'], 0, strrpos($Image['name'], '.')) );
        $this->ImageWidth = ( (int) $ImageWidth ? $ImageWidth : 1024 );
        $this->Folder = ( (string) $Folder ? $Folder : 'images' );

        $this->CheckFolder($this->Folder);
        $this->setFileName();
        $this->UploadImage();
    }

    /**
     * Enviar Arquivo com um tamanho personalizado.
     * Caso não informe o tamanho será 2mb!
     * @param FILES $File = Envia arquivos pelo $_FILES (PDF ou DOCX)
     * @param STRING $Name = Nome do arquivo ( ou do artigo )
     * @param STRING $Folder = Pasta personalizada
     * @param STRING $MaxFileSize = Tamanho máximo do arquivo (2mb)
     */
    public function File(array $File, $Name = null, $Folder = null, $MaxFileSize = null) {
        $this->File = $File;
        $this->Name = ( (string) $Name ? $Name : substr($File['name'], 0, strrpos($File['name'], '.')) );
        $this->Folder = ( (string) $Folder ? $Folder : 'files' );
        $MaxFileSize = ( (int) $MaxFileSize ? $MaxFileSize : 2 );

        $FileAccept = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf'
        ];

        if ($this->File['size'] > ($MaxFileSize * (1024 * 1024))):
            $this->Result = false;
            $this->Error = "Arquivo muito grande, tamanho máximo permitido de {$MaxFileSize}mb";
        elseif (!in_array($this->File['type'], $FileAccept)):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo não suportado. Envie .PDF ou .DOCX!';
        else:
            $this->CheckFolder($this->Folder);
            $this->setFileName();
            $this->MoveFile();
        endif;
    }

    /**
     * Caso não informe o tamanho será 40mb!
     * @param FILES $Media = Enviar envelope de $_FILES (MP3 ou MP4)
     * @param STRING $Name = Nome do arquivo ( ou do artigo )
     * @param STRING $Folder = Pasta personalizada
     * @param STRING $MaxFileSize = Tamanho máximo do arquivo (40mb)
     */
    public function Media(array $Media, $Name = null, $Folder = null, $MaxFileSize = null) {
        $this->File = $Media;
        $this->Name = ( (string) $Name ? $Name : substr($Media['name'], 0, strrpos($Media['name'], '.')) );
        $this->Folder = ( (string) $Folder ? $Folder : 'medias' );
        $MaxFileSize = ( (int) $MaxFileSize ? $MaxFileSize : 40 );

        $FileAccept = [
            'audio/mp3',
            'video/mp4'
        ];

        if ($this->File['size'] > ($MaxFileSize * (1024 * 1024))):
            $this->Result = false;
            $this->Error = "Arquivo muito grande, tamanho máximo permitido de {$MaxFileSize}mb";
        elseif (!in_array($this->File['type'], $FileAccept)):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo não suportado. Envie audio MP3 ou vídeo MP4!';
        else:
            $this->CheckFolder($this->Folder);
            $this->setFileName();
            $this->MoveFile();
        endif;
    }

    /**
     * @return STRING  = Caminho e Nome do arquivo ou False
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

    private function CheckFolder($Folder) {
        list($y, $m) = explode('/', date('Y/m'));
        $this->CreateFolder("{$Folder}");
        $this->CreateFolder("{$Folder}/{$y}");
        $this->CreateFolder("{$Folder}/{$y}/{$m}/");
        $this->Send = "{$Folder}/{$y}/{$m}/";
    }

    private function CreateFolder($Folder) {
        if (!file_exists(self::$BaseDir . $Folder) && !is_dir(self::$BaseDir . $Folder)):
            mkdir(self::$BaseDir . $Folder, 0777);
        endif;
    }

    private function setFileName() {
        $FileName = Check::urlFormat($this->Name) . strrchr($this->File['name'], '.');
        if (file_exists(self::$BaseDir . $this->Send . $FileName)):
            $FileName = Check::urlFormat($this->Name) . '-' . time() . strrchr($this->File['name'], '.');
        endif;
        $this->Name = $FileName;
    }

    private function UploadImage() {

        switch ($this->File['type']):
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $this->Image = imagecreatefromjpeg($this->File['tmp_name']);
                break;
            case 'image/png':
            case 'image/x-png':
                $this->Image = imagecreatefrompng($this->File['tmp_name']);
                break;
        endswitch;

        if (!$this->Image):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
        else:
            $x = imagesx($this->Image);
            $y = imagesy($this->Image);
            $ImageX = ( $this->ImageWidth < $x ? $this->ImageWidth : $x );
            $ImageH = ($ImageX * $y) / $x;

            $NewImage = imagecreatetruecolor($ImageX, $ImageH);
            imagealphablending($NewImage, false);
            imagesavealpha($NewImage, true);
            imagecopyresampled($NewImage, $this->Image, 0, 0, 0, 0, $ImageX, $ImageH, $x, $y);

            switch ($this->File['type']):
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/pjpeg':
                    imagejpeg($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;
                case 'image/png':
                case 'image/x-png':
                    imagepng($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;
            endswitch;

            if (!$NewImage):
                $this->Result = false;
                $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
            else:
                $this->Result = $this->Send . $this->Name;
                $this->Error = null;
            endif;

            imagedestroy($this->Image);
            imagedestroy($NewImage);
        endif;
    }

    private function MoveFile() {
        if (move_uploaded_file($this->File['tmp_name'], self::$BaseDir . $this->Send . $this->Name)):
            $this->Result = $this->Send . $this->Name;
            $this->Error = null;
        else:
            $this->Result = false;
            $this->Error = 'Erro ao mover o arquivo. Favor tente mais tarde!';
        endif;
    }

}
