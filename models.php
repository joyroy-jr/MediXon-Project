<?php
interface ModelInterface {
    public function create($data); public function findById($id);
    public function update($id,$data); public function delete($id); public function getAll();
}
abstract class BaseModel {
    protected $db,$table;
    public function __construct($db){$this->db=$db;}
    protected function sanitize($data){$o=[];foreach($data as $k=>$v)$o[$k]=$this->db->escape($v);return $o;}
    protected function buildInsert($data){$c=implode(',',array_keys($data));$v="'".implode("','",$data)."'";return "INSERT INTO {$this->table}($c)VALUES($v)";}
    protected function buildUpdate($id,$data){$s=[];foreach($data as $k=>$v)$s[]="$k='$v'";return "UPDATE {$this->table} SET ".implode(',',$s)." WHERE id=".((int)$id);}
    abstract protected function validate($d);
}

class User extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='users';}
    public function create($d){
        if(!$this->validate($d))return false;
        $p=$d['password'];$d=$this->sanitize($d);$d['password']=password_hash($p,PASSWORD_BCRYPT);
        return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;
    }
    public function findById($id){$r=$this->db->query("SELECT * FROM users WHERE id=".((int)$id));return $r?$r->fetch_assoc():null;}
    public function findByEmail($e){$e=$this->db->escape($e);$r=$this->db->query("SELECT * FROM users WHERE email='$e'");return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM users WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT id,name,email,phone,location,role,created_at FROM users ORDER BY created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function authenticate($email,$pw){$u=$this->findByEmail($email);if($u&&password_verify($pw,$u['password']))return $u;return false;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM users WHERE role='user'");$row=$r->fetch_assoc();return $row['c'];}
    protected function validate($d){return !empty($d['name'])&&!empty($d['email'])&&!empty($d['password'])&&filter_var($d['email'],FILTER_VALIDATE_EMAIL);}
}

class Equipment extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='equipment';}
    public function create($d){if(!$this->validate($d))return false;$d=$this->sanitize($d);return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;}
    public function findById($id){$id=(int)$id;$r=$this->db->query("SELECT e.*,u.name as owner_name,u.phone as owner_phone FROM equipment e LEFT JOIN users u ON e.user_id=u.id WHERE e.id=$id");return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM equipment WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT e.*,u.name as owner_name FROM equipment e LEFT JOIN users u ON e.user_id=u.id ORDER BY e.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getByUserId($uid){$uid=(int)$uid;$r=$this->db->query("SELECT * FROM equipment WHERE user_id=$uid ORDER BY created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM equipment");$row=$r->fetch_assoc();return $row['c'];}
    public function search($f){
        $w=[];
        if(!empty($f['search'])){$s=$this->db->escape($f['search']);$w[]="(e.name LIKE '%$s%' OR e.company LIKE '%$s%')";}
        if(!empty($f['category'])){$c=$this->db->escape($f['category']);$w[]="e.category='$c'";}
        if(!empty($f['condition'])){$c=$this->db->escape($f['condition']);$w[]="e.condition_type='$c'";}
        if(!empty($f['mode'])){$m=$this->db->escape($f['mode']);$w[]="e.mode='$m'";}
        $wc=$w?'WHERE '.implode(' AND ',$w):'';
        $r=$this->db->query("SELECT e.*,u.name as owner_name FROM equipment e LEFT JOIN users u ON e.user_id=u.id $wc ORDER BY e.created_at DESC");
        $o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;
    }
    protected function validate($d){return !empty($d['name'])&&!empty($d['category'])&&!empty($d['user_id']);}
}

class Request extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='requests';}
    public function create($d){
        if(!$this->validate($d))return false;
        if($this->isDup($d['equipment_id'],$d['requester_id']))return 'DUPLICATE';
        $d=$this->sanitize($d);return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;
    }
    public function findById($id){$id=(int)$id;$r=$this->db->query("SELECT r.*,e.name as equipment_name,u.name as requester_name FROM requests r LEFT JOIN equipment e ON r.equipment_id=e.id LEFT JOIN users u ON r.requester_id=u.id WHERE r.id=$id");return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM requests WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT r.*,e.name as equipment_name,u.name as requester_name FROM requests r LEFT JOIN equipment e ON r.equipment_id=e.id LEFT JOIN users u ON r.requester_id=u.id ORDER BY r.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getByOwnerId($id){$id=(int)$id;$r=$this->db->query("SELECT r.*,e.name as equipment_name,u.name as requester_name FROM requests r LEFT JOIN equipment e ON r.equipment_id=e.id LEFT JOIN users u ON r.requester_id=u.id WHERE r.owner_id=$id ORDER BY r.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getByRequesterId($id){$id=(int)$id;$r=$this->db->query("SELECT r.*,e.name as equipment_name FROM requests r LEFT JOIN equipment e ON r.equipment_id=e.id WHERE r.requester_id=$id ORDER BY r.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM requests");$row=$r->fetch_assoc();return $row['c'];}
    private function isDup($eqId,$reqId){$eqId=(int)$eqId;$reqId=(int)$reqId;$r=$this->db->query("SELECT id FROM requests WHERE equipment_id=$eqId AND requester_id=$reqId");return $r&&$r->num_rows>0;}
    protected function validate($d){return !empty($d['equipment_id'])&&!empty($d['requester_id'])&&!empty($d['owner_id']);}
}

class Medicine extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='medicines';}
    public function create($d){if(!$this->validate($d))return false;$d=$this->sanitize($d);return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;}
    public function findById($id){$id=(int)$id;$r=$this->db->query("SELECT m.*,u.name as owner_name,u.phone as owner_phone FROM medicines m LEFT JOIN users u ON m.user_id=u.id WHERE m.id=$id");return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM medicines WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT m.*,u.name as owner_name FROM medicines m LEFT JOIN users u ON m.user_id=u.id ORDER BY m.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getByUserId($uid){$uid=(int)$uid;$r=$this->db->query("SELECT * FROM medicines WHERE user_id=$uid ORDER BY created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM medicines");$row=$r->fetch_assoc();return $row['c'];}
    public function search($f){
        $w=[];
        if(!empty($f['search'])){$s=$this->db->escape($f['search']);$w[]="(m.name LIKE '%$s%' OR m.generic_name LIKE '%$s%' OR m.manufacturer LIKE '%$s%')";}
        if(!empty($f['category'])){$c=$this->db->escape($f['category']);$w[]="m.category='$c'";}
        if(!empty($f['dosage_form'])){$d=$this->db->escape($f['dosage_form']);$w[]="m.dosage_form='$d'";}
        if(!empty($f['mode'])){$m=$this->db->escape($f['mode']);$w[]="m.mode='$m'";}
        $wc=$w?'WHERE '.implode(' AND ',$w):'';
        $r=$this->db->query("SELECT m.*,u.name as owner_name FROM medicines m LEFT JOIN users u ON m.user_id=u.id $wc ORDER BY m.created_at DESC");
        $o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;
    }
    protected function validate($d){return !empty($d['name'])&&!empty($d['category'])&&!empty($d['user_id']);}
}

class MedicineRequest extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='medicine_requests';}
    public function create($d){
        if(!$this->validate($d))return false;
        if($this->isDup($d['medicine_id'],$d['requester_id']))return 'DUPLICATE';
        $d=$this->sanitize($d);return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;
    }
    public function findById($id){$id=(int)$id;$r=$this->db->query("SELECT r.*,m.name as medicine_name,u.name as requester_name FROM medicine_requests r LEFT JOIN medicines m ON r.medicine_id=m.id LEFT JOIN users u ON r.requester_id=u.id WHERE r.id=$id");return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM medicine_requests WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT r.*,m.name as medicine_name,u.name as requester_name FROM medicine_requests r LEFT JOIN medicines m ON r.medicine_id=m.id LEFT JOIN users u ON r.requester_id=u.id ORDER BY r.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getByOwnerId($id){$id=(int)$id;$r=$this->db->query("SELECT r.*,m.name as medicine_name,u.name as requester_name FROM medicine_requests r LEFT JOIN medicines m ON r.medicine_id=m.id LEFT JOIN users u ON r.requester_id=u.id WHERE r.owner_id=$id ORDER BY r.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getByRequesterId($id){$id=(int)$id;$r=$this->db->query("SELECT r.*,m.name as medicine_name FROM medicine_requests r LEFT JOIN medicines m ON r.medicine_id=m.id WHERE r.requester_id=$id ORDER BY r.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM medicine_requests");$row=$r->fetch_assoc();return $row['c'];}
    private function isDup($mId,$rId){$mId=(int)$mId;$rId=(int)$rId;$r=$this->db->query("SELECT id FROM medicine_requests WHERE medicine_id=$mId AND requester_id=$rId");return $r&&$r->num_rows>0;}
    protected function validate($d){return !empty($d['medicine_id'])&&!empty($d['requester_id'])&&!empty($d['owner_id']);}
}

class Message extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='messages';}
    public function create($d){if(!$this->validate($d))return false;$d=$this->sanitize($d);return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;}
    public function findById($id){$r=$this->db->query("SELECT * FROM messages WHERE id=".((int)$id));return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM messages WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT * FROM messages ORDER BY created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getConversation($u1,$u2){$u1=(int)$u1;$u2=(int)$u2;$r=$this->db->query("SELECT * FROM messages WHERE (sender_id=$u1 AND receiver_id=$u2) OR (sender_id=$u2 AND receiver_id=$u1) ORDER BY created_at ASC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function getChatUsers($uid){$uid=(int)$uid;$r=$this->db->query("SELECT DISTINCT CASE WHEN sender_id=$uid THEN receiver_id ELSE sender_id END as user_id,u.name,u.profile_pic FROM messages m LEFT JOIN users u ON(CASE WHEN m.sender_id=$uid THEN m.receiver_id ELSE m.sender_id END=u.id) WHERE sender_id=$uid OR receiver_id=$uid ORDER BY m.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM messages");$row=$r->fetch_assoc();return $row['c'];}
    protected function validate($d){return !empty($d['sender_id'])&&!empty($d['receiver_id'])&&(!empty($d['message'])||!empty($d['attachment']));}
}

class Feedback extends BaseModel implements ModelInterface {
    public function __construct($db){parent::__construct($db);$this->table='feedback';}
    public function create($d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildInsert($d))?$this->db->getLastInsertId():false;}
    public function findById($id){$r=$this->db->query("SELECT * FROM feedback WHERE id=".((int)$id));return $r?$r->fetch_assoc():null;}
    public function update($id,$d){$d=$this->sanitize($d);return $this->db->executeQuery($this->buildUpdate($id,$d));}
    public function delete($id){return $this->db->executeQuery("DELETE FROM feedback WHERE id=".((int)$id));}
    public function getAll(){$r=$this->db->query("SELECT f.*,u.name as user_name FROM feedback f LEFT JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC");$o=[];while($row=$r->fetch_assoc())$o[]=$row;return $o;}
    public function count(){$r=$this->db->query("SELECT COUNT(*) as c FROM feedback");$row=$r->fetch_assoc();return $row['c'];}
    protected function validate($d){return !empty($d['message']);}
}
