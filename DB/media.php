<?php
require_once 'database_interface.php';
require_once 'DB_routiens.php';

class Media extends DB_object {
    public const MEDIA_NAME= 5;
    public const MEDIA_LOCALURL= 50;
    public const MEDIA_GLOBALURL= 500;
    public const MEDIA_IMG= 6;
    public const MEDIA_TAG_ADD= 60;
    public const MEDIA_TAG_REMOVE= 600;
    public const MEDIA_TAG_REMOVE_ALL= 601;
    public const MEDIA_TAG_TOGGLE= 7;

    private int $id;
    private int $uid;

    // create new media object with id of it on database table and user id on its parent table
    // and established a connection to mysql server
    public function __construct(int $media_id, int $user_id, ?mysqli $conn) {
        parent::__construct($conn);
        $this->id  = $media_id;
        $this->uid =  $user_id;
    }
    // end this object and close any private connection to mysql server
    public function __destruct() {
        parent::__destruct();
    }
    // update all media column depending on $type value
    // $ type acceptes :
    //      Media::MEDIA_LOCALURL       +  url(string)
    //      Media::MEDIA_GLOBALURL      +  url(string)
    //      Media::MEDIA_NAME           +  name(string)
    //      Media::MEDIA_IMG            +  url(string)
    //      Media::MEDIA_TAG_ADD        +  text(string)
    //      Media::MEDIA_TAG_REMOVE     +  text(string)
    //      Media::MEDIA_TAG_TOGGLE     +  text(string)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function update(int $type,string|int ...$data):bool {
        switch($type) {
            case self::MEDIA_NAME:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `media` SET `name`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MEDIA_LOCALURL:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `media` SET `localurl`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MEDIA_GLOBALURL:
                // validate
                validate_url($data[0]);
                return $this->update_routien("UPDATE `media` SET `weburl`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MEDIA_IMG:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `media` SET `img`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MEDIA_TAG_ADD:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `media` SET `tags`=CONCAT(`tags`,'{',?,'}') WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MEDIA_TAG_REMOVE:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `media` SET `tags`=REPLACE(`tags`,CONCAT('{',?,'}'),'') WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::MEDIA_TAG_REMOVE_ALL:
                // validate
                // validate_text($data[0]);
                return $this->update_routien("UPDATE `media` SET `tags`=DEFAULT WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",'null');
            case self::MEDIA_TAG_TOGGLE:
                // validate
                validate_text($data[0]);
                $arr = $this->show();
                if(is_array($arr))
                    if(strpos($arr['tags'],$data[0]))
                        return $this->update_routien("UPDATE `media` SET `tags`=REPLACE(`tags`,CONCAT('{',?,'}'),'') WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
                    else
                        return $this->update_routien("UPDATE `media` SET `tags`=CONCAT(`tags`,'{',?,'}') WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            default:
                return false;
        }
    }
    // return an associative array for media details
    // for the argument, just ignore it, this method do not see it
    public function show(int $type=self::THIS_OBJCT,int $order=self::THIS_OBJCT, int $start=0, int $limit=0, string $match=''):array|false|null {
        // prepare
        // $this->prepare_start();
        $res = $this->connection->query("SELECT `name`,`weburl`,`localurl`,`tags`,`img`,`date` FROM `media` WHERE `id`='{$this->id}' AND `user`='{$this->uid}'");
        $this->is_query_resault($res);
        $val = $res->fetch_assoc();
        // $this->prepare_end();
        $res->close();
        if($val === null || $val === false)
            return $val;
        // retrieve sanitize
        $val['name']        = retrieve_sanitize($val['name']);
        $val['weburl']      = retrieve_sanitize($val['weburl']);
        $val['localurl']    = retrieve_sanitize($val['localurl']);
        $val['tags']        = retrieve_sanitize($val['tags']);
        $val['img']         = retrieve_sanitize($val['img']);
        $val['date']        = retrieve_sanitize($val['date']);
        return $val;
    }
    // this function delete the value of the colomn that refere to it by $type
    // this function uses update method
    // $ type acceptes :
    //      Media::MEDIA_LOCALURL       +  ignore
    //      Media::MEDIA_GLOBALURL      +  ignore
    //      Media::MEDIA_NAME           +  ignore
    //      Media::MEDIA_IMG            +  ignore
    //      Media::MEDIA_TAG_ADD        +  ignore
    //      Media::MEDIA_TAG_REMOVE     +  ignore
    //      Media::MEDIA_TAG_TOGGLE     +  ignore
    // and then path empty string to overwrite
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function delete(int $type, int ...$data):bool {
        return $this->update($type,'');
    }
    // this function is alias to update method
    // update all media column depending on $type value
    // $ type acceptes :
    //      Media::MEDIA_LOCALURL       +  url(string)
    //      Media::MEDIA_GLOBALURL      +  url(string)
    //      Media::MEDIA_NAME           +  name(string)
    //      Media::MEDIA_IMG            +  url(string)
    //      Media::MEDIA_TAG_ADD        +  text(string)
    //      Media::MEDIA_TAG_REMOVE     +  text(string)
    //      Media::MEDIA_TAG_TOGGLE     +  text(string)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function add(int $type, string|int ...$data):bool {
        return $this->update($type,$data[0]);
    }
}