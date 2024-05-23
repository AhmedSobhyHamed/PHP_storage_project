<?php
// this a handler version 2
class HTML_TEMPLATE {
    protected $keyStart;
    protected $keyEnd;
    protected $htmlfile;    // have the sorce html template
    // start object with delimeter
    public function __construct(string $start = '<#{{' ,string $end = '}}#>') {
        $this->keyStart = $start;
        $this->keyEnd = $end;
    }
    // get file path/name to analize 
    // can used with try catch construt
    public function get_file (string $fn) {
        if(file_exists($fn)) {
            $filehandeler = fopen($fn , "r") or throw new Exception("NFO");   //now file open
            flock($filehandeler , LOCK_SH);
            fseek($filehandeler , 0 ,SEEK_SET);
            $tmp = null;
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
    // add data direct to the file string and return true only if all replacement equal or more than the count of variables with the same name in template 
    // if the argument pathed to $valuses is been string then it effect html template in mono variables position
    // like : xx <{{key}}> xx   => xx values xx  || xx <{{  key }}> xx   => xx values xx
    // receive arguments like : ('title','hello');
    // if the argument pathed to $valuses is been array then it effect html template in loop variables position
    // like : <{{LOOP (key as loopvar)}}> .....<{{loopvar->name}}> ... <{{LOOP_END}}> => .....val ....
    // receive arguments like : ('key',[['name'=>'val']['name'=>'val']]);
    public function add_data(string $key , array|string $values): bool {
        if(isset($this->htmlfile)) {
            if(is_string($values)) {
                preg_match("/{$this->keyStart} *$key *{$this->keyEnd}/",$this->htmlfile,$match);
                if(isset($match[0])) {
                    $replacevalue = preg_replace("/{$this->keyStart} *{$key} *{$this->keyEnd}/", $values, $this->htmlfile,-1,$count);
                    if($count>0) {
                        $this->htmlfile = $replacevalue;
                        return true;
                    }
                    return false;
                }
                return false;
            }
            if(is_array($values)) {
                preg_match("/{$this->keyStart} *LOOP *\( *{$key} +as +.+?\) *{$this->keyEnd}.+?{$this->keyStart} *LOOP_END *{$this->keyEnd}/s",$this->htmlfile,$theloop);
                if(isset($theloop[0])) {
                    preg_match("/{$this->keyStart} *LOOP *\( *{$key} +as +.+?\) *{$this->keyEnd}/s",$theloop[0],$element);
                    $element[0] = preg_replace("/{$this->keyStart} *LOOP *\( *{$key} +as/s","",$element[0]);
                    $element[0] = preg_replace("/\) *{$this->keyEnd}/s","",$element[0]);
                    $element[0] = preg_replace("/[^a-zA-Z0-9_]/s",'',$element[0]);
                    $theloop[0] = preg_replace("/{$this->keyStart} *LOOP *\( *{$key} +as +.+?\) *{$this->keyEnd}/s",'',$theloop[0]);
                    $theloop[0] = preg_replace("/{$this->keyStart} *LOOP_END *{$this->keyEnd}/s",'',$theloop[0]);
                    $loop = '';
                    $loopcount = 0;
                    $count = 0;
                    foreach($values as $value) {
                        $loop .= $theloop[0];
                        foreach($value as $k => $v) {
                            $loop = preg_replace("/{$this->keyStart} *{$element[0]}->{$k} *{$this->keyEnd}/s",$v,$loop,-1,$c);
                            $loopcount++;
                            $count += $c;
                        }
                    }
                    if($count >= $loopcount) {
                        $this->htmlfile = preg_replace("/{$this->keyStart} *LOOP *\( *{$key} +as +.+?\) *{$this->keyEnd}.+?{$this->keyStart} *LOOP_END *{$this->keyEnd}/s",$loop,$this->htmlfile);
                        return true;
                    }
                    return false;
                }
                return false;
            }
        }
        return false;
    }
    // return the html page with data ready to print as string
    public function get_page(): string|false {
        if(isset($this->htmlfile)) {
            return $this->htmlfile;
        }
        return false;
    }
}