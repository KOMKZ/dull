<?php
namespace common\helpers;

class ExifTool{
    private $file = null;
    private $metaData = [];
    private $source = "";
    private $bin = "/usr/bin/exiftool";
    public function __construct($file){
        $this->load($file);
    }
    public function load($file){
        if(file_exists($file) && !is_dir($file)){
            $this->file = escapeshellarg($file);
            $this->execute();
            if(array_key_exists('Error', $this->metaData)){
                $this->metaData = [];
            }
            return $this;
        }else{
            throw new \Exception("file doesn't exists and cant be a dir");
        }
    }
    public function getMetaData(){
        return $this->metaData;
    }
    public function getValue($type){
        if(array_key_exists($type, $this->metaData)){
            return $this->metaData[$type];
        }else{
            return null;
        }
    }
    public function getFileExt(){
        if(!empty($this->metaData)){
            return $this->metaData['FileTypeExtension'];
        }
    }
    public function getMasterMime(){
        if(!empty($this->metaData)){
            list($type, ) = explode("/", $this->metaData['MIMEType']);
            return $type;
        }else{
            return null;
        }
    }
    public function getMimeType(){
        if(!empty($this->metaData)){
            return $this->metaData['MIMEType'];
        }
    }
    public function getFileSize(){
        if(!empty($this->metaData)){
            return $this->metaData['FileSize'];
        }
    }
    public function getDocPage(){
        if($this->isMSWord()){
            return $this->metaData['Pages'];
        }elseif($this->isMSPpt()){
            return $this->metaData['Slides'];
        }elseif($this->isPdf()){
            return $this->metaData['PageCount'];
        }elseif($this->isWPSWord() || $this->isWPSPpt()){
            return null;
        }else{
            return null;
        }
    }
    public function getMSWordPage(){
        if($this->isMSWord()){
            return $this->metaData['Pages'];
        }
    }
    public function getMSPptPage(){
        if($this->isMSPpt()){
            return $this->metaData['Slides'];
        }
    }
    public function getPdfPage(){
        if($this->isPdf()){
            return $this->metaData['PageCount'];
        }
    }
    public function getDuration(){
        if($this->isAudio() || $this->isVideo() &&  array_key_exists('Duration', $this->metaData)){
            if(preg_match('/([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/', $this->metaData['Duration'], $matches)){
                if(isset($matches[1])){
                    $hours = (int)$matches[1];
                    $minutes = (int)$matches[2];
                    $seconds = (int)$matches[3];
                    return $hours * 3600 + $minutes * 60 + $seconds;
                }
            }
        }else{
            return null;
        }
    }
    public function isMimeType($type){
        if(!empty($this->metaData)){
            return 0 === substr_compare($this->metaData['MIMEType'], $type, 0, strlen($type));
        }else{
            return false;
        }
    }
    public function isVideo(){
        return true;
    }
    public function isAudio(){
        if(!empty($this->metaData)){
            return 0 === substr_compare($this->metaData['MIMEType'], 'audio', 0, 5);
        }else{
            return false;
        }
    }
    public function isImage(){
        if(!empty($this->metaData)){
            return 0 === substr_compare($this->metaData['MIMEType'], 'image', 0, 5);
        }else{
            return false;
        }
    }
    public function isPdf(){
        if(!empty($this->metaData)){
            return 'PDF' === $this->metaData['FileType'];
        }
    }
    public function isMSWord(){
        if(!empty($this->metaData)){
            $status = false !== strpos($this->metaData['MIMEType'], 'msword') ||
                      false !== strpos($this->source, 'Microsoft');
            return $status &&
                    in_array($this->metaData['FileType'], ['DOC', 'DOCX']);
        }else{
            return false;
        }
    }
    public function isMSPpt(){
        if(!empty($this->metaData)){
            $candicates = [
                'application/mspowerpoint',
                'application/vnd.ms-powerpoint',
                'application/powerpoint',
                'application/mspowerpoint',
                // exiftool check pps file without extension name would get this mine type
                'image/vnd.fpx'
            ];
            $status = false !== strpos($this->source, 'Microsoft') ||
                      in_array($this->metaData['MIMEType'], $candicates);
            return $status &&
                    in_array($this->metaData['FileType'], ['PPT', 'PPTX', 'PPS', 'PPSX', 'FPX']);
        }else{
            return false;
        }
    }
    public function isWPSWord(){
        if(!empty($this->metaData) && array_key_exists('Application', $this->metaData)){
            return strpos($this->metaData['Application'], 'WPS') !== false &&
                   in_array($this->metaData['FileType'], ['DOC', 'DOCX']);
        }else{
            return false;
        }
    }
    public function isWPSPpt(){
        if(!empty($this->metaData) && array_key_exists('Application', $this->metaData)){
            return strpos($this->metaData['Application'], 'WPS') !== false &&
                   in_array($this->metaData['FileType'], ['PPT', 'PPTX', 'PPS', 'PPSX']);
        }else{
            return false;
        }
    }
    protected function execute(){
        // dont use -php argument
        $bin = implode(' ', [$this->bin, trim($this->file), '-json']);
        $this->source = shell_exec($bin);
        $data = json_decode($this->source, true);
        return $this->metaData = $data[0];
    }


}
