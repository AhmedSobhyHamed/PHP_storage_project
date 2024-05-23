<?php

class HTML_TEMPLATE {
    protected $keyStart;
    protected $keyEnd;
    protected $keyArray;    //have array of all keys in the template
    protected $htmlArray;   // sigments of html as array splited from the key possion
    protected $htmlfile;    // have the sorce html template
    protected $htmlpage;    // have the final string of html with data
    // protected $filehandeler;
    // start object with delimeter
    public function __construct(string $start = '<#{{' ,string $end = '}}#>') {
        $this->keyStart = $start;
        $this->keyEnd = $end;
    }
    // get file path/name to analize 
    // can used with try catch construt
    public function get_file ($fn) {
        if(file_exists($fn)) {
            $filehandeler = fopen($fn , "r") or throw new Exception("NFO");   //now file open
            flock($filehandeler , LOCK_SH);
            fseek($filehandeler , 0 ,SEEK_SET);
            if(!($this->htmlfile = file_get_contents($fn))) {
                $tmp = "throw";
            }
            flock($filehandeler , LOCK_UN);
            fclose($filehandeler);
            if($tmp === "throw") {
                throw new Exception("FNF"); //file not found
            }
        }
        else {
            throw new Exception("FNF"); //file not found
        }
    }
    // analize html file into array of sections and array of keys ready to replace with data
    public function analize(): int|false {
        if(isset($this->htmlfile)) {
            $this->htmlArray = preg_split("/{$this->keyStart}.+?{$this->keyEnd}/",$this->htmlfile);
            preg_match_all("/{$this->keyStart}.+?{$this->keyEnd}/",$this->htmlfile,$this->keyArray);
            $this->keyArray = $this->keyArray[0];
            $this->keyArray = str_replace($this->keyStart , "" , $this->keyArray);
            $this->keyArray = str_replace($this->keyEnd , "" , $this->keyArray);
            return count($this->keyArray);
        }
        return false;
    }
    // add data array from parameter to html array and return html page as string
    // use it after analize function to split the template to array ready to used with data
    public function add_data(array $ar): string|false {
        if(count($ar) == count($this->keyArray)) {
            $tmparray = [];
            foreach($this->keyArray as $key) {
                foreach($ar as $ele=>$val) {
                    if($key === $ele) {
                        $tmparray[$key] = $val;
                        unset($ar[$key]);
                        break;
                    }
                }
            }
            if(count($ar) > 0) {
                return false;
            }
            if(count($this->htmlArray) === count($tmparray)+1) {
                $this->htmlpage = "";
                reset($tmparray);
                reset($this->htmlArray);
                $this->htmlpage .= current($this->htmlArray);
                $this->htmlpage .= current($tmparray);
                while($vlu2 = next($tmparray)) {
                    $this->htmlpage .= next($this->htmlArray);
                    $this->htmlpage .= $vlu2;
                }
                $vlu = next($this->htmlArray);
                $this->htmlpage .= $vlu;
                return $this->htmlpage;
            }
        }
        return false;
    }
    // return the html page with data ready to print as string
    public function get_page(): string|false {
        if(isset($this->htmlpage)) {
            return $this->htmlpage;
        }
        return false;
    }
}