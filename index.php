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
            $head_url = '476542084.jpg';
            $time = date("Y-m-d H:i:s");
            $type = 1;
            $friend = '';
            $sql="insert into usermanage(user_name,salt,hash,head_url,type,friends,time) values('".$post['userName']."','".$salt."','".$hash."','".$head_url."','".$type."','".$friend."','".$time."')";
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
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from usermanage where id='{$post['id']}'";
        $rows = $db->fetchOne($sql,$link);
        $data = array();
        if($rows){
            unset($rows['hash']);
            unset($rows['salt']);
            $data['data'] = $rows;
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '该账号不存在！';
        }
        echo $db->encodeJson($data);
    }
    //修改密码
    public function editPassword(){
        $post = $_POST;
        $salt = md5(mcrypt_create_iv(32));
        $password = $post['password'].$salt;
        $hash = md5($password);
        $db = new DB();
        $link = $db->connect();
        $sql="update usermanage set salt='".$salt."' , hash='".$hash."' where id='{$post['id']}'";
        $rows = $db->update($link,$sql);
        $data = array();
        if($rows){
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '失败';
        }
        echo $db->encodeJson($data);
    }
    //修改用户名
    public function editUserName(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="update usermanage set user_name='{$post['user_name']}' where id='{$post['id']}'";
        $rows = $db->update($link,$sql);
        $data = array();
        if($rows){
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '失败';
        }
        echo $db->encodeJson($data);
    }
    //修改头像
    public function editUserPic(){
        try {
            $post = $_POST;
            //方式一：电脑上传文件
            $image = $_FILES["file"]["tmp_name"];
            $fp = fopen($image, "r");
            $file = fread($fp, $_FILES["file"]["size"]); //二进制数据流
            //保存地址
            $imgDir = './img/head/';
            //要生成的图片名字
            if(!file_exists($imgDir)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($imgDir, 0700);
            }
            $filename = date("Ym").md5(time().mt_rand(10, 99)).".jpg"; //新图片名称
            $newFilePath = $imgDir.$filename;
            $data = $file;
            $newFile = fopen($newFilePath,"w"); //打开文件准备写入
                fwrite($newFile,$data); //写入二进制流到文件
                fclose($newFile); //关闭文件
            $db = new DB();
            $link = $db->connect();
            $sql="update usermanage set head_url = '{$filename}' where id='{$post['id']}'";
            print_r($sql);
            $db->update($link,$sql);

          } catch (Exception $e) {
            echo json_encode($e);
          }
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
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from usermanage where id='{$post['id']}'";
        $rows = $db->fetchOne($sql,$link);
        $friendsList = explode(",",$rows['friends']);
        // print_r($friendsList);
        $data = array();
        if($friendsList[0] != ''){
            for($i = 0;$i<count($friendsList);$i++){
                // print_r($friendsList[$i]);
                $sql="select * from usermanage where id='{$friendsList[$i]}'";
                $data['data'][$i] = $db->fetchOne($sql,$link);
            }
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '暂无好友';
        }
        echo $db->encodeJson($data);
    }
    //删除好友
    public function delOneFriend(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from usermanage where id='{$post['id']}'";
        $rows = $db->fetchOne($sql,$link);
        $friendsList = explode(",",$rows['friends']);
        foreach($friendsList as $k=>$v){
            if($v == $post['firendID']){
              unset($friendsList[$k]);
            }
          }
        $arrList = '';
        if($friendsList){
            $arrList = implode(",", $friendsList);
        }
        $data = array();
        $sql="update usermanage set friends = '{$arrList}' where id='{$post['id']}'";
        $rows = $db->update($link,$sql);
        if($rows){
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '删除失败';

        }
        echo $db->encodeJson($data);
    }
    //添加好友
    public function addOneFriend(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $data = array();
        $sql="select * from usermanage where user_name='{$post['friendName']}'";
        $rows = $db->fetchOne($sql,$link);
        if($rows){
            $new_id = $rows['id'];
        }else{
            $data['errcode'] = "不存在该用户";
            echo $db->encodeJson($data);
            return false;
        }
        $sql="select * from usermanage where id='{$post['id']}'";
        $rows = $db->fetchOne($sql,$link);
        $friendsList = explode(",",$rows['friends']);
        foreach($friendsList as $k=>$v){
            if($v == $new_id){
                $data['errcode'] = "已经是好友";
                echo $db->encodeJson($data);
                return false;
            }
        }
        if($friendsList[0] == ''){
            $arrList = $new_id;
        }else{
            array_push($friendsList,$new_id);
            $arrList = '';
            $arrList = implode(",", $friendsList);
        }
        $sql="update usermanage set friends = '{$arrList}' where id='{$post['id']}'";
        $rows = $db->update($link,$sql);
        if($rows){
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '添加失败';

        }
        echo $db->encodeJson($data);
    }
    
}



$app = new Index();
$app->checkAction();

?>