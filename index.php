<?php
require_once 'mySqlFunction.php';
require_once 'mySqlConfig.php';
class Index{
    public $a;
    public function checkAction(){
        // $a = isset($_GET['a']) ? $_GET['a']
        $url = $_SERVER['REQUEST_URI'];
        $actionPath=explode('/',$url);
        $a = $actionPath[3];
        if($a){
            $this->$a();
        }else{
            return 'error';
        }
       
    }
    //注册
    public function logon(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from usermanage where user_name='{$post['userName']}'";
        $rows = $db->fetchOne($sql,$link);
        $data = array();
        if($rows){
            $data['errcode'] = '账号已存在';
        }else{
            $salt = md5(mcrypt_create_iv(32));
            $password = $post['password'].$salt;
            $hash = md5($password);
            //默认普通权限
            // 1 普通 2超级管理员
            $head_url = '../src/head/476542084.jpg';
            $time = date("Y-m-d H:i:s");
            $type = 1;
            $sql="insert into usermanage(user_name,salt,hash,head_url,type,time) values('".$post['userName']."','".$salt."','".$hash."','".$head_url."','".$type."','".$time."')";
            $link = $db->connect();
            $rows = $db->insert($link,$sql);
            if($rows){
                $data['errcode'] = 0;
            }else{
                $data['errcode'] = '注册失败';
            }
        }
        echo $db->encodeJson($data);
    }
    //登录
    public function login(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from usermanage where user_name='{$post['userName']}'";
        $rows = $db->fetchOne($sql,$link);
        $data = array();
        if($rows){
            $salt = $rows['salt'];
            $password = $post['password'].$salt;
            $password = md5($password);
            if($password == $rows['hash']){
                $data['errcode'] = 0;
            }else{
                $data['errcode'] = '密码错误';
            }
        }else{
            $data['errcode'] = '该账号不存在！';
        }
        echo $db->encodeJson($data);
    }
    //查看个人信息
    public function userOne(){

    }
    //修改密码
    public function editPassword(){

    }
    //修改用户名
    public function editUserName(){

    }
    //修改头像
    public function editUserPic(){

    }
    //查看消息
    public function showAllMessage(){

    }
    //查看所有图像
    public function showUploadPic(){

    }
    //上传图像
    public function uploadPic(){

    }
    //删除图像
    public function delOnepic(){

    }
    //查询所有标注
    public function showAllMark(){

    }
    //新添标注
    public function addOneMark(){

    }
    //修改标注
    public function editOneMark(){

    }
    //删除标注
    public function delOneMark(){

    }
    //查询某标注所有留言
    public function showAllMessageByMark(){
   
    }
    //新添留言
    public function addOneMessageByMark(){
   
    }
    //查看所有好友
    public function showAllFriends(){
   
    }
    //删除好友
    public function delOneFriend(){
   
    }
    //查询所有人
    public function selectAllUser(){
        $data = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from usermanage where id='{$data['id']}'";
        $rows = $db->fetchAll($sql,$link);
        $data = array();
        $data['data'] = $rows;
        if($rows){
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = 404;
        }
        $data = $db->encodeJson($data);
        echo $data;
    } 
}



$app = new Index();
$app->checkAction();

?>