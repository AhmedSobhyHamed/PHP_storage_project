<?php

class FILE_UPLOADED {
    protected $directory;
    protected $name;
    protected $size;
    protected $type;
    protected $types;
    protected $max_size;
    protected $ext;
    // begin with max size in KB and then enter all allowed types in MIME form
    public function __construct($dir , $mxsize=1000 , ...$typs) {
        $this->directory = $dir;
        $this->max_size = $mxsize;
        foreach($typs as $t) {
            array_push($this->types , $t);
        }
    }
    // utilite for checking if the size is under the max size allowed
    protected function valid_size():bool {
        if(isset($this->size)) {
            if($this->size < $this->max_size && $this->size > 0) {
                return true;
            }
        }
        return false;
    }
    // utilite for checking if the file type is allowed
    protected function valid_type():bool {
        if(isset($this->type)) {
            reset($this->types);
            foreach($this->types as $t) {
                if($this->type === $t) {
                    return true;
                }
            }
        }
        return false;
    }
    // utilite for checking if the file name is occupied
    protected function is_occupied():bool {
        if(file_exists("{$this->directory}/{$this->name}")) {
            return false;
        }
        return true;
    }
    // utilite for checking if the file name is allowed and force to lower case
    protected function valid_name ():bool {
        $this->name = preg_replace("/\..*/","",$this->name);
        $this->name = strtolower($this->name);
        if(preg_match("/[^a-z0-9_\-]/",$this->name)){
            return false;
        }
        return true;
    }
    // utilite for set extension , must used after type checking
    protected function set_extension() {
        switch($this->type) {
            case "application/pdf":             $this->ext = "pdf";     break;
            case "application/zip":             $this->ext = "zip";     break;
            case "application/x-bzip":          $this->ext = "bz";      break;
            case "application/x-bzip2":         $this->ext = "bz2";     break;
            case "application/gzip":            $this->ext = "gz";      break;
            case "application/vnd.rar":         $this->ext = "rar";     break;
            case "application/x-tar":           $this->ext = "tar";     break;
            case "application/x-7z-compressed": $this->ext = "7z";      break;
            case "audio/mpeg":                  $this->ext = "mpeg";    break;
            case "audio/x-wav":
            case "audio/wav":                   $this->ext = "wav";     break;
            case "audio/mpeg":                  $this->ext = "mp3";     break;
            case "image/gif":                   $this->ext = "gif";     break;
            case "image/jpeg":
            case "image/jpg":                   $this->ext = "jpg";     break;
            case "image/png":                   $this->ext = "png";     break;
            case "image/tiff":                  $this->ext = "tiff";    break;
            case "image/bmp":                   $this->ext = "bmp";     break;
            case "image/vnd.microsoft.icon":    $this->ext = "ico";     break;
            case "text/css":                    $this->ext = "css";     break;
            case "text/htm":
            case "text/html":                   $this->ext = "html";    break;
            case "application/xhtml+xml":       $this->ext = "xhtml";   break;
            case "text/javascript":             $this->ext = "js";      break;
            case "text/plain":                  $this->ext = "txt";     break;
            case "text/xml":                    $this->ext = "xml";     break;
            case "text/csv":                    $this->ext = "csv";     break;
            case "video/mpeg":                  $this->ext = "mpeg";    break;
            case "video/mp4":                   $this->ext = "mp4";     break;
            case "video/x-msvideo":             $this->ext = "avi";     break;
            default:                            $this->ext = "";        break;
        }
    }
    // utilite for checking if the file is valid in name , size , type and name not duplicated
    public function validate ():bool {
        $tmp = false;
        $tmp = $this->valid_size();
        $tmp = $this->valid_type();
        $tmp = $this->valid_name();
        $tmp = $this->is_occupied();
        $this->set_extension();
        return $tmp;
    }
    // change the name for the file , if you use it, must used before validations steps 
    public function change_filename($n) {
        $this->name = $n;
    }
    // generate a name for the file
    public function generate_name(?string $user) {
        $tm = date("Y-M-d-H-i-s",time());
        if(isset($user)) {
        $tm = $user . $tm . sprintf("-%04d",rand(0,9999));
        }
        else {
        $tm = "file-" . $tm . sprintf("-%04d",rand(0,9999));
        }
        $this->name = $tm;
    }
    // get file info
    public function get_file($fn):string|bool {
        if($_FILES && !$_FILES[$fn]["error"]) {
            $this->name = $_FILES[$fn]["name"];
            $this->type = $_FILES[$fn]["type"];
            $this->size = $_FILES[$fn]["size"];
        }
        else {
            $this->name = "";
            $this->type = "";
            $this->size = -1;
            return  $_FILES[$fn]["error"];
        }
        return true;
    }
    // create the file and move it from tmp folder to choosen directory and name
    public function create_file($fn):bool {
        if($this->validate()) {
            if(!move_uploaded_file($_FILES[$fn]["tmp_name"],"{$this->directory}/{$this->name}.{$this->ext}")) {
                return false;
            }
            return true;
        }
        return false;
    }
}
