<?php
require_once 'database_interface.php';
require_once 'DB_routiens.php';

class Manga extends DB_object {
    public const MANGA_URL= 5;
    public const MANGA_IMG= 10;
    public const MANGA_DSC= 50;
    public const MANGA_chp= 500;

    private int $id;
    private int $uid;


    // create new manga object with id of it on database table and user id on its parent table
    // and established a connection to mysql server
    public function __construct(int $manga_id, int $user_id, ?mysqli $conn) {
        parent::__construct($conn);
        $this->id  = $manga_id;
        $this->uid =  $user_id;
    }
    // end this object and close any private connection to mysql server
    public function __destruct() {
        parent::__destruct();
    }
    // update all manga column depending on $type value
    // $ type acceptes :
    //      Manga::MANGA_URL    +  url(string)
    //      Manga::MANGA_IMG    +  url(string)
    //      Manga::MANGA_DSC    +  text(string)
    //      Manga::MANGA_CHP    +  number(int)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server 
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function update(int $type,string|int ...$data):bool {
        switch($type) {
            case self::MANGA_URL:
                // validate
                validate_url($data[0]);
                return $this->update_routien("UPDATE `manga` SET `url`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MANGA_IMG:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `manga` SET `img`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MANGA_DSC:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `manga` SET `description`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MANGA_chp:
                // validate
                validate_number($data[0]);
                return $this->update_routien("UPDATE `manga` SET `chapter`=? , `date`=DEFAULT WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            default:
                return false;
        }
    }
    // return an associative array for manga details
    // for the argument, just ignore it, this method do not see it
    public function show(int $type=self::THIS_OBJCT,int $order=self::THIS_OBJCT, int $start=0, int $limit=0, string $match=''):array|false|null {
        // prepare
        // $this->prepare_start();
        $res = $this->connection->query("SELECT `name`,`url`,`img`,`description`,`chapter`,`date` FROM `manga` WHERE `id`='{$this->id}' AND `user`='{$this->uid}'");
        $this->is_query_resault($res);
        $val = $res->fetch_assoc();
        $res->close();
        // $this->prepare_end();
        if($val === null || $val === false)
            return $val;
        // retrieve sanitize
        $val['name']        = retrieve_sanitize($val['name']);
        $val['url']         = retrieve_sanitize($val['url']);
        $val['img']         = retrieve_sanitize($val['img']);
        $val['description'] = retrieve_sanitize($val['description']);
        $val['chapter']     = retrieve_sanitize($val['chapter']);
        $val['date']        = retrieve_sanitize($val['date']);
        return $val;
    }
    // this function delete the value of the colomn that refere to it by $type
    // this function uses update method
    // $ type acceptes :
    //      Manga::MANGA_URL    +  ignore
    //      Manga::MANGA_IMG    +  ignore
    //      Manga::MANGA_DSC    +  ignore
    //      Manga::MANGA_CHP    +  ignore
    // and then path empty string to overwrite
    // and then send it to mysql server 
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function delete(int $type, int ...$data):bool {
        return $this->update($type,'');
    }
    // this function is alias to update method
    // update all manga column depending on $type value
    // $ type acceptes :
    //      Manga::MANGA_URL    +  url(string)
    //      Manga::MANGA_IMG    +  url(string)
    //      Manga::MANGA_DSC    +  text(string)
    //      Manga::MANGA_CHP    +  number(int)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server 
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function add(int $type,string|int ...$data):bool {
        return $this->update($type,$data[0]);
    }
}