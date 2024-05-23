<?php
require_once 'database_interface.php';
require_once 'DB_routiens.php';


class Notes extends DB_object {
    public const NOTE_NAME = 900;
    public const SNIPPET   = 877;

    private int $id;
    private int $uid;

    // create new note object with id of it on database table and user id on its parent table
    // and established a connection to mysql server
    public function __construct(int $note_id, int $user_id, ?mysqli $conn) {
        parent::__construct($conn);
        $this->id  = $note_id;
        $this->uid =  $user_id;
    }
    // end this object and close any private connection to mysql server
    public function __destruct() {
        parent::__destruct();
    }
    // update all notes column or child snippets depending on $type value
    // $ type acceptes :
    //      Notes::NOTE_NAME        +  title(string)
    //      Notes::SNIPPET          +  text(string) + snippet number(int)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function update(int $type,string|int ...$data):bool {
        switch($type) {
            case self::NOTE_NAME:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `notes` SET `title`=? WHERE `id`='{$this->id}' AND `user`='{$this->uid}'",$data[0]);
            case self::SNIPPET:
                // validate
                validate_text($data[0]);
                validate_number($data[1]);
                return $this->update_routien("UPDATE `snippet` SET `text`=? WHERE `id`=? AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='{$this->uid}')",$data[0],$data[1]);
            default:
                return false;
        }
    }
    // return an associative array for notes details and its child snippets
    // for the argument, just ignore it, this method do not see it
    public function show(int $type=self::THIS_OBJCT,int $order=self::THIS_OBJCT, int $start=0, int $limit=0, string $match=''):array|false|null {
        // prepare
        // $this->prepare_start();
        $res = $this->connection->query("SELECT `title` AS 'name',`date` FROM `notes` WHERE `id`='{$this->id}' AND `user`='{$this->uid}'");
        $this->is_query_resault($res);
        $val = $res->fetch_assoc();
        if($val === null || $val === false)
            return $val;
        // retrieve sanitize
        $val['name']        = retrieve_sanitize($val['name']);
        $val['date']        = retrieve_sanitize($val['date']);
        $res->close();
        // get snippets one by one
        $res = $this->connection->query("SELECT `text`,`id` FROM `snippet` WHERE `note`='{$this->id}' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='{$this->uid}')");
        $this->is_query_resault($res);
        for($r=0 ; $r < $res->num_rows ; $r++)
        {
            $v = $res->fetch_assoc();
            if($v === null)
                return $val;
            if($v === false)
                return false;
            $val[$v['id']]  = retrieve_sanitize($v['text']);
        }
        // $this->prepare_end();
        $res->close();
        return $val;
    }
    // this function delete the snippet that id number is provided in $id argumnt
    // for the second argument just ignore it
    // then validate and sanitize this number
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function delete(int $id, int ...$data):bool {
                // validate
                validate_number($id);
                return $this->update_routien("DELETE FROM `snippet` WHERE `id`=? AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='{$this->uid}')",$id);
    }
    // add new snippets to a note depending on $type value
    // $ type acceptes :
    //      Notes::SNIPPET          +  text(string)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function add(int $type,string|int ...$data):bool {
        switch($type) {
            case self::SNIPPET:
                // validate
                validate_text($data[0]);
                return $this->update_routien("INSERT INTO `snippet`(`text`,`note`) SELECT ? AS `text`,'{$this->id}' AS `note` WHERE '{$this->id}' IN (SELECT `id` FROM `notes` WHERE `user`='{$this->uid}')",$data[0]);
            default:
                return false;
        }
    }
}