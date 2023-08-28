<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        $qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./");
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            $save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_department(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `department_list` (`name`,`description`)VALUES('{$name}','{$description}')";
        else
            $sql = "UPDATE `department_list` set `name` = '{$name}',`description` = '{$description}' where `rowid` = '{$id}' ";
        $save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Department successfully saved.";
            else
                $resp['msg'] = "Department successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Department Failed.";
            else
                $resp['msg'] = "Updating Department Failed.";
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_department(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `department_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Department successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        $check = $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            $save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `user_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_issue(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(isset($cols) && isset($values)){
            $cols[] = 'user_id';
            $values[] = "'{$_SESSION['user_id']}'";
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
       
        
        if(empty($id)){
            $sql = "INSERT INTO `issue_list` {$data}";
        }else{
            $sql = "UPDATE `issue_list` set {$data} where issue_id = '{$id}'";
        }
        $save = $this->query($sql);
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
            $resp['msg'] = 'New Issue successfully saved.';
            else
            $resp['msg'] = 'Issue Details successfully updated.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Saving Issue Details Failed. Error: '.$this->lastErrorMsg();
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function delete_issue(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `issue_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Issue successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_comment(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(isset($cols) && isset($values)){
            $cols[] = 'user_id';
            $values[] = "'{$_SESSION['user_id']}'";
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
       
        
        if(empty($id)){
            $sql = "INSERT INTO `comment_list` {$data}";
        }else{
            $sql = "UPDATE `comment_list` set {$data} where comment_id = '{$id}'";
        }
        $save = $this->query($sql);
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
            $resp['msg'] = 'New Comment successfully saved.';
            else
            $resp['msg'] = 'Comment Details successfully updated.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Saving Comment Details Failed. Error: '.$this->lastErrorMsg();
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function delete_comment(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `comment_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Comment successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function close_issue(){
        extract($_POST);

       ///sendmail///
        $mailtoid="SELECT user_id from `issue_list` where issue_id = '{$id}' ";
        $qry1= $this->query($mailtoid)->fetchArray();
        $ansid=$qry1['user_id'];


        $mailtoid1="SELECT title from `issue_list` where user_id = '{$ansid}' ";
        $qry2= $this->query($mailtoid1)->fetchArray();
        $name=$qry2['title'];

        $mailtoid9="SELECT email from `user_list` where user_id = '{$ansid}' ";
        $qry9= $this->query($mailtoid9)->fetchArray();
        $em=$qry9['email'];

        

        $mailtoid3="SELECT department_id from `issue_list` where issue_id = '{$id}' ";
        $qry3= $this->query($mailtoid3)->fetchArray();
        $dept=$qry3['department_id'];

        
        $mailtoid4="SELECT name from `department_list` where rowid = '{$dept}' ";
        $qry4= $this->query($mailtoid4)->fetchArray();
        $deptna=$qry4['name'];
      

    $issue_title="";
        $delete = $this->query("UPDATE `issue_list` set `status` = 1 where issue_id = '{$id}'");
      
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Issue successfully closed.';
  
            //change email
$to = "souradyuti.choudhury2020@vitstudent.ac.in";
$to=$em;
$subject = "HTML email";

$message = "
<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>Your issue $name has been solved by $deptna .</p>

</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: souradyutichoudhury10@gmail.com' . "\r\n";


mail($to,$subject,$message,$headers);

        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_department':
        echo $action->save_department();
    break;
    case 'delete_department':
        echo $action->delete_department();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'save_issue':
        echo $action->save_issue();
    break;
    case 'delete_issue':
        echo $action->delete_issue();
    break;
    case 'save_comment':
        echo $action->save_comment();
    break;
    case 'delete_comment':
        echo $action->delete_comment();
    break;
    case 'close_issue':
        echo $action->close_issue();
    break;
    default:
    // default action here
    break;
}