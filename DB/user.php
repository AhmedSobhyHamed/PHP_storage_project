<?php
require_once 'database_interface.php';
require_once 'DB_routiens.php';

class User extends DB_object {
    public const NAME       = 35;
    public const PHOTO      = 32;
    public const MEDIA      = 325;
    public const MANGA      = 326;
    public const NOTES      = 328;
    public const ALL        = 330;
    public const SEARCH     = 332;
    public const DATE_ASC   = 438;
    public const DATE_DSC   = 458;
    public const TITLE_ASC  = 478;
    public const MATCH_RATE = 479;

    private int $id;
    private string $name;
    private string $image;
    private bool $deleteme;

    // create new user object with id of it on database table
    // and established a connection to mysql server
    // parameter can provided is:
    // mysqli, email, password
    // mysqli, email, password, username
    // mysqli, email, password, username, photo
    // after takes the parameters 
    // if you provide email, password. it search for it in database and return its id if found or throw exception
    // if provided data is contains username. it search in database for the email you provide 
    // and if found throw exception that represent the user is allready found, and if not it create new one
    public function __construct(?mysqli $conn, string ...$data) {
        parent::__construct($conn);
        $this->deleteme = false;
        if(isset($data[0]) && isset($data[1])) {
            // validate
            validate_email($data[0]);
            validate_password($data[1]);
            if(isset($data[2])) validate_text($data[2]);
            if(isset($data[3])) validate_url($data[3]);
            // sanitize 
            $email = sanitize($this->connection , $data[0]);
            $paswd = sanitize($this->connection , $data[1]);
            if(isset($data[2])) $name = sanitize($this->connection , $data[2]);
            if(isset($data[3])) $image = sanitize($this->connection , $data[3]);
            // prepare
            $this->prepare_start(); 
            $stmt = $this->connection->prepare('SELECT `name`,`password`,`photo`,`id` FROM `users` WHERE `email`=?');
            $stmt->bind_param('s', $email);
            if(!$stmt->execute())
                throw new Exception('DB connection error');
            $res = $stmt->get_result();
            $stmt->close();
            $this->prepare_end();
            $this->is_query_resault($res);
            $val = $res->fetch_assoc();
            $res->close();
            // if email and password only provided
            if(!isset($data[2])) {
                if($val === null)
                    throw new Exception('email not found');
                if($val === false)
                    throw new Exception('DB connection error');
                if(!verify_hash_string($paswd , $val['password']))
                    throw new Exception('password incorrect');
                $this->name = $val['name'];
                $this->image = is_null($val['photo']) ? '' : $val['photo'];
                $this->id = $val['id'];
            }
            // else then create new user
            else {
                if($val === false)
                    throw new Exception('DB connection error');
                if($val !== null)
                    throw new Exception('email exist');
                // prepare
                $this->prepare_start(); 
                if(!isset($image)) {
                    $stmt = $this->connection->prepare('INSERT INTO `users`(`name`,`email`,`password`,`photo`) VALUES(?,?,?,DEFAULT)');
                    $hsh_pwd = hash_string($paswd);
                    $stmt->bind_param('sss', $name, $email, $hsh_pwd);
                }
                else {
                    $stmt = $this->connection->prepare('INSERT INTO `users`(`name`,`email`,`password`,`photo`) VALUES(?,?,?,?)');
                    $hsh_pwd = hash_string($paswd);
                    $stmt->bind_param('sss', $name, $email, $hsh_pwd, $image);
                }
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $stmt->close();
                $stmt = $this->connection->prepare('SELECT `name`,`password`,`photo`,`id` FROM `users` WHERE `email`=?');
                $stmt->bind_param('s', $email);
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $res = $stmt->get_result();
                $stmt->close();
                $this->is_query_resault($res);
                $val = $res->fetch_assoc();
                $res->close();
                $this->prepare_end();
                if($val === null)
                    throw new Exception('email not found');
                if($val === false)
                    throw new Exception('DB connection error');
                if(!verify_hash_string($paswd , $val['password']))
                    throw new Exception('password incorrect');
                $this->name = $val['name'];
                $this->image = is_null($val['photo']) ? '' : $val['photo'];
                $this->id = $val['id'];
            }
        }
        else
            throw new Exception('missing parameters');
        // $this->id  = $user_id;
    }
    // end this object and close any private connection to mysql server
    public function __destruct() {
        if($this->deleteme)
            $this->update_routien("DELETE FROM `users` WHERE `id`=? ",$this->id);
        parent::__destruct();
    }
    // return user's id if user found or false if not found
    public function id():string|false {
        if(isset($this->id))
            return $this->id;
        return false;
    }
    // return user's name if user found or false if not found
    public function name():string|false {
        if(isset($this->name))
            return $this->name;
        return false;
    }
    // return user's image url if user found or false if not found
    public function image():string|false {
        if(isset($this->image))
            return $this->image;
        return false;
    }
    // delete this user from users table and delete all dependent data in any table when destruct is called 
    public function deleteThisUser():void {
        $this->deleteme = true;
    }
    // update user name or photo columns depending on $type value
    // $ type acceptes :
    //      User::NAME        +  name(string)
    //      User::PHOTO        +  url(string)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function update(int $type,string|int ...$data):bool {
        switch($type) {
            case self::NAME:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `users` SET `name`=? WHERE `id`='{$this->id}'",$data[0]);
            case self::PHOTO:
                // validate
                validate_text($data[0]);
                return $this->update_routien("UPDATE `users` SET `photo`=? WHERE `id`='{$this->id}'",$data[0]);
            default:
                return false;
        }
    }
    // return an array of associative array for notes, manga and media details that related to this user
    // for the argument, it accepte this combinations
    //      User::NOTES        +  {User::DATE_ASC|User::DATE_DSC|User::TITLE_ASC} + number(int) + number(int) + ignore
    //      User::MEDIA        +  {User::DATE_ASC|User::DATE_DSC|User::TITLE_ASC} + number(int) + number(int) + ignore
    //      User::MANGA        +  {User::DATE_ASC|User::DATE_DSC|User::TITLE_ASC} + number(int) + number(int) + ignore
    //      User::ALL          +  {User::DATE_ASC|User::DATE_DSC|User::TITLE_ASC} + number(int) + number(int) + ignore
    //      User::SEARCH       +  {User::DATE_ASC|User::DATE_DSC|User::TITLE_ASC|User::MATCH_RATE} + number(int) + number(int) + text(string)
    //and then its check for validating of parameters data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function show(int $type=self::ALL,int $order=self::TITLE_ASC, int $start=0, int $limit=9999999, string $match=''):array|false|null {
        // validate
        validate_number($start);
        validate_number($limit);
        validate_text($match);
        // sanitize 
        $start = sanitize($this->connection , $start);
        $limit = sanitize($this->connection , $limit);
        $match = sanitize($this->connection , $match);
        $this->prepare_start(); 

        switch($type) {
            case self::NOTES:
                if($order === self::DATE_ASC)
                    $stmt = $this->connection->prepare("SELECT `title` AS 'name',`id`,`date`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}' ORDER BY `date` ASC LIMIT ?,?");
                if($order === self::DATE_DSC)
                    $stmt = $this->connection->prepare("SELECT `title` AS 'name',`id`,`date`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}' ORDER BY `date` DESC LIMIT ?,?");
                if($order === self::TITLE_ASC)
                    $stmt = $this->connection->prepare("SELECT `title` AS 'name',`id`,`date`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}' ORDER BY `title` ASC LIMIT ?,?");
                $stmt->bind_param('ii', $start, $limit);
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $res = $stmt->get_result();
                $stmt->close();
                $this->prepare_end();
                $this->is_query_resault($res);
                $numrows = $res->num_rows;
                for($i = 0 ; $i < $numrows ; $i++) {
                    $val = $res->fetch_assoc();
                    if($val === null || $val === false)
                        if(!isset($value))
                            return $val;
                        else
                            return $value;
                    // retrieve sanitize
                    $val['name']        = retrieve_sanitize($val['name']);
                    $val['id']          = retrieve_sanitize($val['id']);
                    $val['date']        = retrieve_sanitize($val['date']);
                    $value[$i] = $val;
                }
                $res->close();
                if(isset($value))
                    return $value;
                else
                    return array();
            case self::MANGA:
                if($order === self::DATE_ASC)
                    $stmt = $this->connection->prepare("SELECT `name`,`img`,`description`,`chapter`,`id`,`date`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}' ORDER BY `date` ASC LIMIT ?,?");
                if($order === self::DATE_DSC)
                    $stmt = $this->connection->prepare("SELECT `name`,`img`,`description`,`chapter`,`id`,`date`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}' ORDER BY `date` DESC LIMIT ?,?");
                if($order === self::TITLE_ASC)
                    $stmt = $this->connection->prepare("SELECT `name`,`img`,`description`,`chapter`,`id`,`date`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}' ORDER BY `name` ASC LIMIT ?,?");
                $stmt->bind_param('ii', $start, $limit);
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $res = $stmt->get_result();
                $stmt->close();
                $this->prepare_end();
                $this->is_query_resault($res);
                $numrows = $res->num_rows;
                for($i = 0 ; $i < $numrows ; $i++) {
                    $val = $res->fetch_assoc();
                    if($val === null || $val === false)
                        if(!isset($value))
                            return $val;
                        else
                            return $value;
                    // retrieve sanitize
                    $val['name']        = retrieve_sanitize($val['name']);
                    $val['id']          = retrieve_sanitize($val['id']);
                    $val['date']        = retrieve_sanitize($val['date']);
                    $val['img']         = retrieve_sanitize($val['img']);
                    $val['description'] = retrieve_sanitize($val['description']);
                    $val['chapter']     = retrieve_sanitize($val['chapter']);
                    $value[$i] = $val;
                }
                $res->close();
                if(isset($value))
                    return $value;
                else
                    return array();
            case self::MEDIA:
                if($order === self::DATE_ASC)
                    $stmt = $this->connection->prepare("SELECT `name`,`tags`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}' ORDER BY `date` ASC LIMIT ?,?");
                if($order === self::DATE_DSC)
                    $stmt = $this->connection->prepare("SELECT `name`,`tags`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}' ORDER BY `date` DESC LIMIT ?,?");
                if($order === self::TITLE_ASC)
                    $stmt = $this->connection->prepare("SELECT `name`,`tags`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}' ORDER BY `name` ASC LIMIT ?,?");
                $stmt->bind_param('ii', $start, $limit);
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $res = $stmt->get_result();
                $stmt->close();
                $this->prepare_end();
                $this->is_query_resault($res);
                $numrows = $res->num_rows;
                for($i = 0 ; $i < $numrows ; $i++) {
                    $val = $res->fetch_assoc();
                    if($val === null || $val === false)
                        if(!isset($value))
                            return $val;
                        else
                            return $value;
                    // retrieve sanitize
                    $val['name']        = retrieve_sanitize($val['name']);
                    $val['id']          = retrieve_sanitize($val['id']);
                    $val['date']        = retrieve_sanitize($val['date']);
                    $val['img']         = retrieve_sanitize($val['img']);
                    $val['tags'] = retrieve_sanitize($val['tags']);
                    $value[$i] = $val;
                }
                $res->close();
                if(isset($value))
                    return $value;
                else
                    return array();
            case self::ALL:
                if($order === self::DATE_ASC)
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}') 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}') 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}') 
                    ORDER BY `date` ASC LIMIT ?,?");
                if($order === self::DATE_DSC)
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}') 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}') 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}') 
                    ORDER BY `date` DESC LIMIT ?,?");
                if($order === self::TITLE_ASC)
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}') 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}') 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}') 
                    ORDER BY `name` ASC LIMIT ?,?");
                $stmt->bind_param('ii', $start, $limit);
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $res = $stmt->get_result();
                $stmt->close();
                $this->prepare_end();
                $this->is_query_resault($res);
                $numrows = $res->num_rows;
                for($i = 0 ; $i < $numrows ; $i++) {
                    $val = $res->fetch_assoc();
                    if($val === null || $val === false)
                        if(!isset($value))
                            return $val;
                        else
                            return $value;
                    // retrieve sanitize
                    $val['name']        = retrieve_sanitize($val['name']);
                    $val['id']          = retrieve_sanitize($val['id']);
                    $val['date']        = retrieve_sanitize($val['date']);
                    $val['img']         = retrieve_sanitize($val['img']);
                    $val['description'] = retrieve_sanitize($val['description']);
                    $value[$i] = $val;
                }
                $res->close();
                if(isset($value))
                    return $value;
                else
                    return array();
            case self::SEARCH:
                if($order === self::DATE_ASC)
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}' AND MATCH(`name`,`tags`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}' AND MATCH(`name`,`description`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}' AND MATCH(`title`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    ORDER BY `date` ASC LIMIT ?,?");
                if($order === self::DATE_DSC)
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}' AND MATCH(`name`,`tags`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}' AND MATCH(`name`,`description`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}' AND MATCH(`title`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    ORDER BY `date` DESC LIMIT ?,?");
                if($order === self::TITLE_ASC)
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`,'media' AS `type` FROM `media` WHERE `user`='{$this->id}' AND MATCH(`name`,`tags`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`,'manga' AS `type` FROM `manga` WHERE `user`='{$this->id}' AND MATCH(`name`,`description`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`,'notes' AS `type` FROM `notes` WHERE `user`='{$this->id}' AND MATCH(`title`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    ORDER BY `name` ASC LIMIT ?,?");
                if($order === self::MATCH_RATE) {
                    $stmt = $this->connection->prepare("
                    (SELECT `name`,`tags` AS `description`,`img`,`date`,`id`, 'media' AS `type`, MATCH(`name`,`tags`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE) AS `SEO` FROM `media` WHERE `user`='{$this->id}' AND MATCH(`name`,`tags`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `name`,`description`,`img`,`date`,`id`, 'manga' AS `type`, MATCH(`name`,`description`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE) AS `SEO` FROM `manga` WHERE `user`='{$this->id}' AND MATCH(`name`,`description`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    UNION ALL 
                    (SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`, 'notes' AS `type`, MATCH(`title`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE) AS `SEO` FROM `notes` WHERE `user`='{$this->id}' AND MATCH(`title`) AGAINST(? COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
                    ORDER BY `SEO` DESC LIMIT ?,?");
                    $stmt->bind_param('ssssssii', $match, $match, $match, $match, $match, $match, $start, $limit);
                    goto next_point;
                }
                $stmt->bind_param('sssii', $match, $match, $match, $start, $limit);
                next_point:
                if(!$stmt->execute())
                    throw new Exception('DB connection error');
                $res = $stmt->get_result();
                $stmt->close();
                $this->prepare_end();
                $this->is_query_resault($res);
                $numrows = $res->num_rows;
                for($i = 0 ; $i < $numrows ; $i++) {
                    $val = $res->fetch_assoc();
                    if($val === null || $val === false)
                        if(!isset($value))
                            return $val;
                        else
                            return $value;
                    // retrieve sanitize
                    $val['name']        = retrieve_sanitize($val['name']);
                    $val['id']          = retrieve_sanitize($val['id']);
                    $val['date']        = retrieve_sanitize($val['date']);
                    $val['img']         = retrieve_sanitize($val['img']);
                    $val['description'] = retrieve_sanitize($val['description']);
                    $val['SEO'] = '';
                    $value[$i] = $val;
                }
                $res->close();
                if(isset($value))
                    return $value;
                else
                    return array();
            default:
                return false;
        }
    }
    // delete users dependent data depending on $type value
    // $ type acceptes :
    //      User::NOTES     + id(number)
    //      User::MANGA     + id(number)
    //      User::MEDIA     + id(number)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function delete(int $type, int ...$data):bool {
        switch($type) {
            case self::NOTES:
                // validate
                validate_number($data[0]);
                return $this->update_routien("DELETE FROM `notes` WHERE `id`=? AND `user`='{$this->id}'",$data[0]);
            case self::MANGA:
                // validate
                validate_number($data[0]);
                return $this->update_routien("DELETE FROM `manga` WHERE `id`=? AND `user`='{$this->id}'",$data[0]);
            case self::MEDIA:
                // validate
                validate_number($data[0]);
                return $this->update_routien("DELETE FROM `media` WHERE `id`=? AND `user`='{$this->id}'",$data[0]);
            default:
                return false;
        }
    }
    // insert a new users dependent data depending on $type value
    // $ type acceptes :
    //      User::NOTES     + name(string)
    //      User::MANGA     + name(string) + url(string)
    //      User::MEDIA     + name(string) + url(string)
    //and then its check for validating of data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function add(int $type, string|int ...$data):bool {
        switch($type) {
            case self::NOTES:
                // validate
                validate_text($data[0]);
                return $this->update_routien("INSERT INTO `notes`(`title`,`user`)VALUES(?,'{$this->id}')",$data[0]);
            case self::MANGA:
                // validate
                validate_text($data[0]);
                validate_url($data[1]);
                return $this->update_routien("INSERT INTO `manga`(`name`,`url`,`img`,`description`,`chapter`,`user`)VALUES(?,?,DEFAULT,DEFAULT,DEFAULT,'{$this->id}')",$data[0],$data[1]);
            case self::MEDIA:
                // validate
                validate_text($data[0]);
                validate_url($data[1]);
                return $this->update_routien("INSERT INTO `media`(`name`,`weburl`,`localurl`,`tags`,`img`,`user`)VALUES(?,?,DEFAULT,DEFAULT,DEFAULT,'{$this->id}')",$data[0],$data[1]);
            default:
                return false;
        }
    }
    // return a number of the brevious id from id that pathed as argument 
    // $ type acceptes :
    //      User::NOTES     + id(int)
    //      User::MANGA     + id(int)
    //      User::MEDIA     + id(int)
    //and then its check for validating of parameters data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function get_previous_child_data(int $type, int|string $id):int|false {
        // validate
        validate_number($id);
        // sanitize 
        $id = sanitize($this->connection , $id);
        $this->prepare_start(); 

        switch($type) {
            case self::NOTES:
                $stmt = $this->connection->prepare("SELECT `id` FROM `notes` WHERE `user`='{$this->id}' AND `id` < ?  ORDER BY `id` DESC LIMIT 0,1");
                break;
            case self::MEDIA:
                $stmt = $this->connection->prepare("SELECT `id` FROM `media` WHERE `user`='{$this->id}' AND `id` < ?  ORDER BY `id` DESC LIMIT 0,1");
                break;
            case self::MANGA:
                $stmt = $this->connection->prepare("SELECT `id` FROM `manga` WHERE `user`='{$this->id}' AND `id` < ?  ORDER BY `id` DESC LIMIT 0,1");
                break;
            }
        $stmt->bind_param('i', $id);
        if(!$stmt->execute())
            throw new Exception('DB connection error');
        $res = $stmt->get_result();
        $stmt->close();
        $this->prepare_end();
        $this->is_query_resault($res);
        if($res->num_rows > 0) {
            $val = $res->fetch_assoc();
            if($val === null || $val === false)
                    return false;
            // retrieve sanitize
            $res->close();
            return retrieve_sanitize($val['id']);
        }
        $res->close();
        return false;
    }
    // return a number of the next id from id that pathed as argument 
    // $ type acceptes :
    //      User::NOTES     + id(int)
    //      User::MANGA     + id(int)
    //      User::MEDIA     + id(int)
    //and then its check for validating of parameters data and sanitize it
    // and then send it to mysql server
    // it return true if every thing will and return false if the data not valid
    // and throw an Exception from embedded method if there is error in the connection to mysql server
    public function get_next_child_data(int $type, int|string $id):int|false {
        // validate
        validate_number($id);
        // sanitize 
        $id = sanitize($this->connection , $id);
        $this->prepare_start(); 

        switch($type) {
            case self::NOTES:
                $stmt = $this->connection->prepare("SELECT `id` FROM `notes` WHERE `user`='{$this->id}' AND `id` > ?  ORDER BY `id` ASC LIMIT 0,1");
                break;
            case self::MEDIA:
                $stmt = $this->connection->prepare("SELECT `id` FROM `media` WHERE `user`='{$this->id}' AND `id` > ?  ORDER BY `id` ASC LIMIT 0,1");
                break;
            case self::MANGA:
                $stmt = $this->connection->prepare("SELECT `id` FROM `manga` WHERE `user`='{$this->id}' AND `id` > ?  ORDER BY `id` ASC LIMIT 0,1");
                break;
            }
        $stmt->bind_param('i', $id);
        if(!$stmt->execute())
            throw new Exception('DB connection error');
        $res = $stmt->get_result();
        $stmt->close();
        $this->prepare_end();
        $this->is_query_resault($res);
        if($res->num_rows > 0) {
            $val = $res->fetch_assoc();
            if($val === null || $val === false)
                    return false;
            // retrieve sanitize
            $res->close();
            return retrieve_sanitize($val['id']);
        }
        $res->close();
        return false;
    }
    // get an array of all stored path for user resourses like imgs vieos and files
    public function get_resourses():array|null|false {
        $this->prepare_start(); 
        $stmt = $this->connection->prepare("SELECT `localurl` AS `img` FROM `media` WHERE `user`='{$this->id}' LIMIT 0,9999999");
        if(!$stmt->execute())
            throw new Exception('DB connection error');
        $res = $stmt->get_result();
        $stmt->close();
        $this->prepare_end();
        $this->is_query_resault($res);
        $numrows = $res->num_rows;
        for($i = 0 ; $i < $numrows ; $i++) {
            $val = $res->fetch_assoc();
            if($val === null || $val === false)
                if(!isset($value))
                    return $val;
                else
                    return $value;
            // retrieve sanitize
            $val['img']         = retrieve_sanitize($val['img']);
            $value[$i] = $val['img'];
        }
        $res->close();
        $show = $this->show();
        foreach($show as $s) {
            array_push($value,$s['img']);
        }
        if(isset($value))
            return $value;
        else
            return array();
    }
}