<?php

class Dbase
{
    private static $sqlCon;
    private static $init = false;
    private static $host;
    private static $user;
    private static $pass;
    private static $dbas;
    private static $key;
    
    /* Connect()
    * ----------
    * Establishes a connection to the MySQL server.
    * Returns: a handle to the MySQL connection
    */
    public static function Connect ()
    {
        
        if ( !self::$init )
        {
            if ( isset($_SESSION["settings"]) )
                $config = $_SESSION["settings"];
                
            else
                $config = parse_ini_file("config.ini",true);
                
            $config = $config["database"];
                
            self::$host = $config["host"];
            self::$user = $config["user"];
            self::$pass = $config["pass"];
            self::$dbas = $config["dbas"];
            self::$key  = $config["key"];
            
            /*
            if ( isset($_SESSION["settings"]) )
                unset( $_SESSION["settings"]["database"] );*/
                
            self::$init = true;
        }
    
        self::$sqlCon = mysql_connect(self::$host,self::$user,self::$pass);

        if (self::$sqlCon)
            mysql_select_db(self::$dbas, self::$sqlCon);

        return self::$sqlCon;
    }
     
    /* Disconnect()
    * ----------
    * Closes the connection to the MySQL server if it exists.
    */
    public static function Disconnect ()
    {
        if (self::$sqlCon) mysql_close(self::$sqlCon);
    }
    
    public static function DumpCSV ()
    {
        $user = self::$user;
        $pass = self::$pass;
        $dbas = self::$dbas;
        $host = self::$host;
        $bash = "mysqldump -u $user -h $host -p$pass $dbas";
        return shell_exec($bash);
    }
    
    public static function Encrypt ($string)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::$key), $string, MCRYPT_MODE_CBC, md5(md5(self::$key))));
    }
    
    private static function Decrypt ($string)
    {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::$key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5(self::$key))), "\0");
    }
    
    private static function Query ($query)
    {
        if ( !self::$sqlCon ) return null;

        return mysql_query($query, self::$sqlCon); 
    }
    
    private static function InsertInto ($table, $fields, $values)
    {
        $query = "INSERT INTO $table ($fields) VALUES ($values)";
        
        return self::Query($query);
    }
    
    private static function SelectFromWhere ($select, $from="", $where="")
    {
        $query = "SELECT $select";
        
        if (  ($from != null) && ($from != "") )
            $query = $query . " FROM $from";
        
        if ( ($from != null) && ($from != "") && ($where != null) && ($where != "") )
            $query = $query . " WHERE $where";
        
        $result = self::Query($query);
        
        if ( !$result )
            return null;
        
        $index = 0;

        while ($row = mysql_fetch_array($result))
        {
            $table[$index++] = $row;
        }

        if (!isset($table)) 
            return null;

        return $table;
    }
    
    public static function Updates ($table, $changes, $where)
    {
        $q  = "UPDATE $table SET ";
        $q .= $changes[0]['key'] . "='" . $changes[0]['val'] . "'";
        
        for ($i=1; $i<count($changes); $i++)
        {
            $c = $changes[$i];
            $q .= ", $c[key]='$c[val]'";
        }
        
        $q .= " WHERE $where";
        return self::Query($q);
    }
    
    private static function SelectFromWhereFirst ($select, $from="", $where="")
    {
        $res = self::SelectFromWhere($select,$from,$where);
        
        if ( $res )
            $res = $res[0];
        else
            return null;
        
        return $res;
    }
    
    public static function Authenticate ($username, $enteredPassword)
    {
        $select = "id, password";
        $from = "users";
        $where = "username='$username'";
        
        $user = self::SelectFromWhereFirst($select,$from,$where);
        
        if ( !$user )
            return false;

        $storedPassword = $user["password"];
        
        if ( crypt($enteredPassword, $storedPassword) == $storedPassword )
        {
            // log login
            self::InsertInto("user_logins","user,time","$user[id],NOW()");
        
            return $user["id"];
        }
        

        return false;
    }
    
    public static function Authenticate2 ($email)
    {
        $select = "id";
        $from = "users";
        $where = "email='".self::Encrypt($email)."'";
        
        $user = self::SelectFromWhereFirst($select,$from,$where);

        if ( !$user ){
            self::AddUser($email);
            $user = self::SelectFromWhereFirst($select,$from,$where);
        }
        
        // log login
        self::InsertInto("user_logins","user,time","$user[id],NOW()");

        return $user["id"];

    }
    
    public static function GetRoleRef ()
    {
        $select = "code, role_name";
        $from = "ref_role";
        
        $res = self::SelectFromWhere($select, $from);
        
        if (!$res)
            return null;
        
        foreach ($res as $r)
            $roles[ $r['code'] ] = $r['role_name'];
        
        return $roles;
    }
    
    public static function GetCommentFlagRef ()
    {
        $select = "id, reason";
        $from = "ref_comment_flag";
        
        $res = self::SelectFromWhere($select,$from);
        
        if (!$res)
            return null;
        
        foreach ($res as $f)
            $flag[ $f['id'] ] = $f['reason'];
        
        return $flag;
    }
    
    public static function GetUsers ()
    {
        $select = "id,username,firstname,lastname,email,role_code";
        $from = "users";
        
        $users = self::SelectFromWhere($select, $from);
            
        if ( !$users )
            return null;

        foreach ($users as $k => $u)
        {
            $users[$k]["lastname"] = self::Decrypt($u["lastname"]);
            $users[$k]["email"] = self::Decrypt($u["email"]);
        }
        
        return $users;
    }
    
    public static function GetInstitutions ()
    {
        $inst = self::SelectFromWhere("id,name","institutions");
        if (!$inst) return null;
        return $inst;
    }
    
    public static function GetUserInfo ($userId)
    {
        $select = "firstname,lastname,email,role_code,username,id,institute,major,gpa,school_year,gender,age,race,have_disa,disability";
        $from = "users";
        $where = "id=$userId";
        
        $user =  self::SelectFromWhereFirst($select,$from,$where);
        
        $user["lastname"]    = self::Decrypt($user["lastname"]);
        $user["email"]       = self::Decrypt($user["email"]);
        $user["institute"]   = self::Decrypt($user["institute"]);
        $user["major"]       = self::Decrypt($user["major"]);
        $user["gpa"]         = self::Decrypt($user["gpa"]);
        $user["school_year"] = self::Decrypt($user["school_year"]);
        $user["gender"]      = self::Decrypt($user["gender"]);
        $user["age"]         = self::Decrypt($user["age"]);
        $user["race"]        = self::Decrypt($user["race"]);
        $user["have_disa"]   = self::Decrypt($user["have_disa"]);
        $user["disability"]  = self::Decrypt($user["disability"]);
        
        return $user;
    }
    
    public static function GetSessions ()
    {
        $select = "id,course_id,UNIX_TIMESTAMP(date) as date";
        $from = "sessions ORDER BY date DESC";
        
        return self::SelectFromWhere($select, $from);
    }
    
    public static function GetSessionInfo ($sessionId)
    {
        $select = "course_id,date";
        $from = "sessions";
        $where = "id=$sessionId";
        
        return self::SelectFromWhereFirst($select,$from,$where);
    }
    
    public static function GetCourses ()
    {
        $select = "id,title,term_code,year";
        $from = "courses";
        
        $res = self::SelectFromWhere($select, $from);
        
        if (!$res)
            return null;
        
        foreach($res as $c)
            $courses[ $c['id'] ] = $c;
        
        return $courses;
    }
    
    public static function GetCourseInfo ($courseId)
    {
        $select = "id,title,term_code,year";
        $from = "courses";
        $where = "id=$courseId";
        
        return self::SelectFromWhereFirst($select,$from,$where);
    }
    
    public static function GetCourseFromSession ($sessionId)
    {
        $select = "id,title,term_code,year";
        $from = "courses";
        $where = "id IN (SELECT course_id FROM sessions WHERE id=$sessionId)";
        
        return self::SelectFromWhereFirst($select,$from,$where);
    }
    
    public static function GetComments ()
    {
        $select  = "c.id, c.session_id, c.user_id, ";
        $select .= "c.time, c.comment, c.flag_id, ";
        $select .= "IFNULL(SUM(r.rating),0) as rating";
        $from  = "comments c";
        $from .= " LEFT JOIN comment_ratings r";
        $from .= " ON c.id=r.comment_id";
        $from .= " GROUP BY c.id";
        $from .= " ORDER BY rating DESC";
        
        return self::SelectFromWhere($select,$from);
    }
    
    public static function GetCommentsFromSession ($sessionId)
    {
        $select = "c.id,c.user_id,c.time,c.comment,c.flag_id,IFNULL(SUM(r.rating),0) as rating";
        $from = "comments c";
        $from = $from . " LEFT JOIN comment_ratings r ON c.id = r.comment_id";
        $where = "c.session_id=$sessionId";
        $where = $where . " GROUP BY c.id";
        $where = $where . " ORDER BY rating DESC";
        
        $res = self::SelectFromWhere($select,$from,$where);
        $comments = null;
        
        if ( $res )
        foreach ($res as $c)
            $comments[$c['id']] = $c;
        
        return $comments;
    }
    
    public static function GetCommentRatingsForUser ($userId)
    {
        $select = "comment_id as id, rating";
        $from = "comment_ratings";
        $where = "user_id = $userId";
        
        $res = self::SelectFromWhere($select,$from,$where);
        
        if (!$res)
            return null;
        
        foreach ($res as $v)
            $rates[ $v['id'] ] = $v['rating'];
        
        return $rates;
    }
    
    public static function GetPresents ()
    {
        $select = "id,session_id,name,url";
        $from = "presentations";
        
        $res = self::SelectFromWhere($select,$from);
        
        if (!$res)
            return null;
        
        foreach ($res as $k=>$p)
            $res[$k]["url"] = self::Decrypt($p["url"]);
        
        return $res;
    }
    
    public static function GetPresentsFromSession ($sessionId)
    {
        $select = "id,name,url";
        $from = "presentations";
        $where = "session_id=$sessionId";
        
        $res = self::SelectFromWhere($select,$from,$where);
        
        if (!$res)
            return null;
        
        foreach ($res as $k=>$p)
            $res[$k]["url"] = self::Decrypt($p["url"]);
        
        return $res;
    }
    
    public static function GetEnrollment ()
    {
        $select = "course_id, user_id, role_code";
        $from = "enrollment";
        
        return self::SelectFromWhere($select,$from);
    }
    
    public static function GetEnrollmentFromUser ($userId)
    {
        $select = "course_id, role_code";
        $from = "enrollment";
        $where = "user_id=$userId";
        
        return self::SelectFromWhere($select, $from, $where);
    }
    
    public static function GetUserRoleInCourse ($userId, $courseId)
    {
        $select = "role_code";
        $from = "enrollment";
        $where = "user_id=$userId AND course_id=$courseId";
        
        $res = self::SelectFromWhereFirst($select,$from,$where);
        
        if ( !$res )
            return null;
        
        return $res["role_code"];
    }
    
    public static function AddUser ($email){
        // default role
        $role = 'st';
        
        // Encrypt Last Name & email etc.. (decryptable)
        $lastName   = self::Encrypt("");
        $email      = self::Encrypt($email);
        $inst       = self::Encrypt("");
        $major      = self::Encrypt("");
        $gpa        = self::Encrypt("");
        $schoolYear = self::Encrypt("");
        $sex        = self::Encrypt("");
        $age        = self::Encrypt("");
        $race       = self::Encrypt("");
        $haveDisa   = self::Encrypt("");
        $disability = self::Encrypt("");
        
        // Encrpyt password (one way encryption)
        $password = crypt($password);
        
        $table   = "users";
        $fields  = "id, username, email, firstname, lastname, password, role_code, ";
        $fields .= "institute, major, gpa, school_year, gender, age, race, have_disa, disability, joined";
        $values  = "DEFAULT, '', '$email', '', '$lastName', '$password', '$role', ";
        $values .= "'$inst', '$major', '$gpa', '$schoolYear', '$sex', '$age', '$race', '$haveDisa', '$disability', NOW()";
        
        self::InsertInto($table,$fields,$values);
        
        return $newId = mysql_insert_id();
        
        // self::AddUserToCourse($newId, 28, 'st');
    }
    
    public static function AddCourse ($title, $termCode, $year)
    {
        $table = "courses";
        $fields = "id, title, term_code, year";
        $values = "DEFAULT, '$title', '$termCode', $year";
        
        self::InsertInto($table,$fields,$values);
        
        $select = "LAST_INSERT_ID() AS newId";
        $res = self::SelectFromWhereFirst($select);
        
        return $res["newId"];
    }
    
    public static function RemoveCourse ($courseId)
    {
        self::Query("DELETE FROM courses WHERE id = $courseId");
	self::Query("DELETE FROM enrollment WHERE course_id = $courseId");	

	$select = "id";
	$from = "sessions";
	$where = "course_id = $courseId";
	$sessions = self::SelectFromWhere($select,$from,$where);	
	if ( $sessions ) foreach ($sessions as $s) self::RemoveSession($s['id']);
    }
    
    public static function AddUserToCourse($userId, $courseId, $role)
    {
        $table = "enrollment";
        $fields = "user_id, course_id, role_code";
        $values = "'$userId', '$courseId', '$role'";
        
        return self::InsertInto($table,$fields,$values);
    }
     
    public static function AddSession ($courseId,$unixtime)
    {
        $table = "sessions";
        $fields = "id, course_id, date";
        $values = "DEFAULT, $courseId, DATE( FROM_UNIXTIME($unixtime) )";
        
        self::InsertInto($table,$fields,$values);
        
        return mysql_insert_id();
    }
    
    public static function RemoveSession ($sessionId)
    {
        self::Query("DELETE FROM sessions WHERE id = $sessionId");
	
	$select = "id";
	$from = "presentations";
	$where = "session_id = $sessionId";
	$presents = self::SelectFromWhere($select,$from,$where);
	if ($presents) foreach($presents as $p) self::RemovePresent($p['id']);

	$select = "id";
	$from = "comments";
	$where = "session_id = $sessionId";
	$comments = self::SelectFromWhere($select,$from,$where);
	if ($comments) foreach($comments as $c) self::RemoveComment($c['id']);
    }
     
    public static function AddPresent ($sessionId, $name, $url)
    {
        // Encrypt URL (decryptable)
        $url = self::Encrypt($url);
        
        $table = "presentations";
        $fields = "id, session_id, name, url";
        $values = "DEFAULT, $sessionId, '$name', '$url'";
        
        return self::InsertInto($table,$fields,$values);
    }
    
    public static function RemovePresent ($presentId)
    {
        self::Query("DELETE FROM presentations WHERE id = $presentId");
    }
     
    public static function AddComment ($sessionId, $userId, $comment)
    {
        $table = "comments";
        $fields = "id, session_id, user_id, time, comment, flag_id";
        $values = "DEFAULT, $sessionId, $userId, NOW(), '$comment', DEFAULT";
        
        return self::InsertInto($table,$fields,$values);
    }
    
    public static function EditComment ($commentId, $comment)
    {
        $q  = "UPDATE comments ";
        $q .= "SET comment='$comment', time=NOW() ";
        $q .= "WHERE id=$commentId";
        
        return self::Query($q);
    }
    
    public static function RemoveComment ($commentId)
    {
        self::Query("DELETE FROM comments WHERE id = $commentId");
	self::Query("DELETE FROM comment_ratings WHERE comment_id = $commentId");
    }
    
    public static function RateCommentUp ($userId, $commentId)
    {
        $select = "rating";
        $from = "comment_ratings";
        $where = "user_id=$userId AND comment_id=$commentId";
        $res = self::SelectFromWhereFirst($select, $from, $where);
        
        if ( !$res )
        {
            $table = "comment_ratings";
            $fields = "comment_id, user_id, rating";
            $values = "$commentId, $userId, 1";
            return self::InsertInto($table,$fields,$values);
        }
        
        elseif ( $res['rating'] > 0 )
            return null;
            
        elseif ( $res['rating'] <= 0 )
        {
            $sql  = "UPDATE comment_ratings ";
            $sql .= "SET rating = rating+1 ";
            $sql .= "WHERE user_id = $userId ";
            $sql .= "AND comment_id = $commentId";
            return self::Query($sql);
        }
        
        return null;
    }
    
    public static function RateCommentDown ($userId, $commentId)
    {
        $select = "rating";
        $from = "comment_ratings";
        $where = "user_id=$userId AND comment_id=$commentId";
        $res = self::SelectFromWhereFirst($select, $from, $where);
        
        if ( !$res )
        {
            $table = "comment_ratings";
            $fields = "comment_id, user_id, rating";
            $values = "$commentId, $userId, -1";
            return self::InsertInto($table,$fields,$values);
        }
        
        elseif ( $res['rating'] < 0 )
            return null;
            
        elseif ( $res['rating'] >= 0 )
        {
            $sql  = "UPDATE comment_ratings ";
            $sql .= "SET rating = rating-1 ";
            $sql .= "WHERE user_id = $userId ";
            $sql .= "AND comment_id = $commentId";
            return self::Query($sql);
        }
        
        return null;
    }
    
    public static function FlagComment ($commentId, $flagId)
    {
        $query = "UPDATE comments SET flag_id=$flagId WHERE id=$commentId";
        
        return self::Query($query);
    }
    
    public static function GetQuizzes ($sessionId)
    {
        $q = "
            SELECT
                q.id,
                q.name,
                q.time,
                q.num_options,
                q.open
            FROM quizzes q
            WHERE session = $sessionId
        ";
        $q = self::Query($q);
        
        $qForms = array();
        
        while ($row = mysql_fetch_assoc($q))
            $qForms[$row['id']] = $row;

        $q = "
            SELECT quiz,
                SUM(choice=1) as A,
                SUM(choice=2) as B,
                SUM(choice=3) as C,
                SUM(choice=4) as D,
                SUM(choice=5) as E,
                SUM(choice=6) as F
            FROM
            (
                SELECT quiz,user,choice
                FROM
                (
                    SELECT DISTINCT quiz,user,choice
                    FROM quiz_answers
                    ORDER BY time DESC
                ) t
                GROUP BY quiz,user
            ) s
            WHERE quiz IN (SELECT id FROM quizzes WHERE session = $sessionId)
            GROUP BY quiz
        ";
        
        $q = self::Query($q);
        
        $qChoices = array();
        
        while ($row = mysql_fetch_assoc($q))
            $qChoices[$row['quiz']] = $row;
            
        $ar = array();
        foreach ($qForms as $qId => $qForm)
        {
            $ar[$qId] = array();
            $ar[$qId]['form']    = $qForms[$qId];
            
            if (isset($qChoices[$qId]))
                $ar[$qId]['choices'] = $qChoices[$qId];
            else
                $ar[$qId]['choices'] = array ("quiz"=>$qId, "A"=>0, "B"=>0, "C"=>0, "D"=>0, "E"=>0, "F"=>0 );
        }
            
        return $ar;
    }
    
    public static function GetQuizzesUserAnswered ($userid)
    {
        $q = "SELECT DISTINCT quiz FROM quiz_answers WHERE user = $userid";
        $q = self::Query($q);
        
        $res = array();
        while ($r = mysql_fetch_assoc($q))
            $res[] = $r['quiz'];
            
        return $res;
    }
    
    public static function AddQuiz ($sessionId, $name, $numOptions, $open)
    {
        if ($numOptions < 1) $numOptions = 1;
        if ($numOptions > 6) $numOptions = 6;
        if (!$name || $name=="") $name = "Quiz";
        
        $q = "INSERT INTO quizzes (id, name, time, num_options, open, session)
                VALUES (DEFAULT, '$name', NOW(), $numOptions, $open, $sessionId)";
        
        self::Query($q);
        
        return mysql_insert_id();
    }
    
    public static function RemoveQuiz ($quizId)
    {
        $q = "DELETE FROM quizzes WHERE id = $quizId";
        self::Query($q);
    }
    
    public static function SubmitQuizAnswer ($userId, $quizId, $answer)
    {
        $q = "INSERT INTO quiz_answers (id, quiz, user, choice, time)
            VALUES (DEFAULT, $quizId, $userId, $answer, NOW());";
        
        $q = self::Query($q);
    }
    
    public static function OpenQuiz ($quiz, $open)
    {
        $q = "UPDATE quizzes SET open = $open WHERE id = $quiz";
        self::Query($q);
    }
    
    public static function GetJoinCourseList ()
    {
        $list = array();
        
        $q = "
            SELECT
                c.id, c.title, u.firstname, u.lastname, u.id as user_id
            FROM enrollment e
            RIGHT JOIN courses c ON c.id = e.course_id
            RIGHT JOIN users u ON u.id = e.user_id
            WHERE e.role_code = 'in' AND c.active=1
        ";
        
        $q = self::Query($q);
        while ($r = mysql_fetch_assoc($q))
        {
            $i = $r['id'];
            $t = $r['title'];
            $f = $r['firstname'];
            $l = self::Decrypt($r['lastname']);
            $u = $r['user_id'];
            
            $row = array();
            $row['name'] = "$l, $f";
            $row['course'] = array();
            $row['course']['title'] = $t;
            $row['course']['id'] = $i;
            
            $list[] = $row;
        }
        
        // sort
        for ($i=0; $i<count($list); $i++)
        {
            $changes = 0;
            for ($j=count($list)-1; $j>$i; $j--)
            {
                if ( strcmp($list[$j]['name'],$list[$j-1]['name']) < 0 )
                {
                    $changes++;
                    $tmp = $list[$j];
                    $list[$j] = $list[$j-1];
                    $list[$j-1] = $tmp;
                }
            }
            if ($changes == 0) break;
        }
        
        // reorganize
        $count = count($list);
        $names = -1;
        for ($i=0; $i<$count; $i++)
        {
            $names++;
            $name = $list[$i]['name'];
            $course = $list[$i]['course'];
            unset($list[$i]);
            $list[$names][] = $name;
            $list[$names][] = $course;
            
            for ($j=$i+1; $j<$count; $j++)
            {
                if (strcmp($name,$list[$j]['name']) == 0)
                {
                    $list[$names][] = $list[$j]['course'];
                    unset($list[$j]);
                    if ($j == $count-1)
                    {
                        $i=$j;
                        break;
                    }
                }
                else
                {
                    $i = $j-1;
                    break;
                }
            }
        }
        
        return $list;
    }
    
    public static function IsFirstLogin ($user)
    {
        $q = "SELECT * FROM user_logins WHERE user = $user";
        $q = self::Query($q);
        
        return (mysql_num_rows($q)==1);
    }
    
    public static function requiredFields($user){

        if(!isset($user['username']) || $user['username'] == '')
            return false;
        if(!isset($user['firstname']) || $user['firstname'] == '')
            return false;
        if(!isset($user['lastname']) || $user['lastname'] == '')
            return false;
        if(!isset($user['institute']) || $user['institute'] == '')
            return false;
        if(!isset($user['email']) || $user['email'] == '')
            return false;
        
        return true;
    }
}   

?>
