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
                $data['id'] = $rows['id'];
                $data['userName'] = $rows['user_name'];
                $data['head_url'] = $rows['head_url'];
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
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from imageManage where user_id='{$post['id']}'";
        $rows = $db->fetchAll($sql,$link);
        $data = array();
        if($rows){
            $data['data'] = $rows;
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '暂未上传图像';
        }
        echo $db->encodeJson($data);
    }
    //上传图像
    public function uploadPic(){
        try {
            $post = $_POST;
            //方式一：电脑上传文件
            $image = $_FILES["file"]["tmp_name"];
            $fp = fopen($image, "r");
            $file = fread($fp, $_FILES["file"]["size"]); //二进制数据流
            //保存地址
            $imgDir = './img/image/';
            //要生成的图片名字
            if(!file_exists($imgDir)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($imgDir, 0700);
            }
            $filename = date("Ym").md5(time().mt_rand(10, 99)); //新图片名称
            $url = $filename.".jpg";
            $newFilePath = $imgDir.$filename.".jpg";
            $data = $file;
            $newFile = fopen($newFilePath,"w"); //打开文件准备写入
            fwrite($newFile,$data); //写入二进制流到文件
            fclose($newFile); //关闭文件
            $db = new DB();
            $time = date("Y-m-d H:i:s");
            $link = $db->connect();
            $sql="insert into imagemanage(user_id,img_name,url,time) values('".$post['id']."','".$filename."','".$url."','".$time."')";
            print_r($sql);
            $rows = $db->insert($link,$sql);
            if($rows){
                $data['errcode'] = 0;
            }else{
                $data['errcode'] = '上传失败';
            }
            echo $db->encodeJson($data);


          } catch (Exception $e) {
            echo json_encode($e);
          }
    }
    //删除图像
    public function delOnepic(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="delete from imageManage where id='{$post['id']}'";
        $rows = $db->delete($link,$sql);
        $data = array();
        if($rows){
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '删除失败';
        }
        echo $db->encodeJson($data);
    }
    //查询所有标注
    public function showAllMark(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from markmanage where img_id='{$post['img_id']}'";
        $rows = $db->fetchAll($sql,$link);
        $data = array();
        if($rows){
            $data['data'] = $rows;
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '暂未存在标注';
        }
        echo $db->encodeJson($data);
    }
    //查询已经标注图像
    public function showHadMarkImage(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select * from imagemanage where user_id='{$post['id']}'";
        $rows = $db->fetchAll($sql,$link);
        $data = array();
        if(!$rows){
            $data['errcode'] = '暂无图像';
            echo $db->encodeJson($data);
            return false;
        }
        for($i = 0;$i<count($rows);$i++){
            $sql="select count(*) from markmanage where img_id='{$rows[$i]['id']}'";
            $nums = $db->fetchOne($sql,$link);
            $rows[$i]['nums'] = $nums['count(*)'];
            if($nums['count(*)'] == 0){
                unset($rows[$i]);
            }
        }
        if(!$rows){
            $data['errcode'] = '暂无已标注图像';
            echo $db->encodeJson($data);
            return false;
        }
        $data['errcode'] = 0;
        $data['data'] = $rows;
        echo $db->encodeJson($data);
    }
    //新添标注
    public function addOneMark(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $time = date("Y-m-d H:i:s");
        $data = json_decode($post['data'],true);
        $response = array();
        for($i=0;$i<count($data);$i++){
            $mapData = json_encode($data[$i]);
            $sql="insert into markmanage(user_id,img_id,mapdata,time) values('".$post['userId']."','".$post['img_id']."','".$mapData."','".$time."')";
            $rows = $db->insert($link,$sql);
            if(!$rows){
                $response['errcode'] = '新添失败';
                echo $db->encodeJson($data);
                return false;
            }
        }
        $response['errcode'] = 0;
        echo $db->encodeJson($response);
    }
    //修改标注
    public function editOneMark(){
        $post = $_POST;
        $data = json_decode($post['data'],true);
        $db = new DB();
        $link = $db->connect();
        $time = date("Y-m-d H:i:s");
        $data = json_decode($post['data'],true);
        $response = array();
        for($i=0;$i<count($data);$i++){
            $markId = $data[$i]['mark_id'];
            unset($data[$i]['mark_id']);
            $mapData = json_encode($data[$i]);
            $sql="update markmanage set mapdata ='".$mapData."',time ='".$time."',user_id ='".$post['userId']."' where id ='".$markId."'";
            $rows = $db->update($link,$sql);
            if(!$rows){
                $response['errcode'] = '修改失败';
                echo $db->encodeJson($response);
                return false;
            }
        }
        $response['errcode'] = 0;
        echo $db->encodeJson($response);
    }
    //删除标注
    public function delOneMark(){
        $post = $_POST;
        $data = json_decode($post['data'],true);
        $db = new DB();
        $link = $db->connect();
        $data = json_decode($post['data'],true);
        $response = array();
        for($i=0;$i<count($data);$i++){
            $markId = $data[$i]['mark_id'];
            $sql="delete from markmanage where id ='".$markId."'";
            $rows = $db->update($link,$sql);
            if(!$rows){
                $response['errcode'] = '删除失败';
                echo $db->encodeJson($response);
                return false;
            }
        }
        $response['errcode'] = 0;
        echo $db->encodeJson($response);
    }
    //查询某标注所有留言
    public function showAllMessageByMark(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $sql="select a.user_name,a.head_url,b.content,b.time,b.user_id from usermanage as a,chatManage as b  where a.id=b.user_id and b.mark_id='{$post['markId']}'";
        $rows = $db->fetchAll($sql,$link);
        $data = array();
        if($rows){
            $data['data'] = $rows;
            $data['errcode'] = 0;
        }else{
            // $data['errcode'] = '暂无留言';
            $data['errcode'] = 0;
            echo $db->encodeJson($data);
            return true;
        }
        $sql="select distinct  a.user_name,a.head_url from usermanage as a,chatManage as b  where a.id=b.user_id and b.mark_id='{$post['markId']}'";
        $rowss = $db->fetchAll($sql,$link);
        if($rowss){
            $data['user'] = $rowss;
            $data['errcode'] = 0;
        }else{
            $data['errcode'] = '查看失败';
            echo $db->encodeJson($data);
            return false;
        }
        echo $db->encodeJson($data);
    }
    //新添留言
    public function addOneMessageByMark(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $time = date("Y-m-d H:i:s");
        $response = array();
        $sql="insert into chatmanage(mark_id,user_id,content,time) values('".$post['mark_id']."','".$post['user_id']."','".$post['content']."','".$time."')";
        $row = $db->insert($link,$sql);
        if($row){
            $response['errcode'] = 0;
        }else{
            $response['errcode'] = '发送失败';
        }
        echo $db->encodeJson($response);
    }
    //查看好友分享的图像
    public function showFriendsShare(){
        $post = $_POST;
        $db = new DB();
        $link = $db->connect();
        $response = array();
        $sql="select * from usermanage where id='{$post['id']}'";
        $rows = $db->fetchOne($sql,$link);
        // echo $rows;
        // die();
        if(!$rows){
            $response['errcode'] = '暂无好友';
            echo $db->encodeJson($response);
            return false;
        }
        $data = array();
        $friendList = explode(",",$rows['friends']);
        // print_r($friendList);
        // die();
        
        $imgList=[];
        for($i = 0; $i<count($friendList);$i++){
            $sqlUserName = "select user_name from usermanage where id='{$friendList[$i]}'";
            $userName = $db->fetchOne($sqlUserName,$link);
            $sql="select * from imagemanage where user_id='{$friendList[$i]}'";
            $test = $db->fetchAll($sql,$link,$userName['user_name']);
            array_push($imgList,$test);
        }
        if($imgList){
            $response['errcode'] = 0;
            $response['data'] = $imgList;
        }else{
            $response['errcode'] = '你的好友暂无上传的图像';
            echo $db->encodeJson($response);
            return false;
        }
        echo $db->encodeJson($response);
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
        $data = array();

        $sql="select * from usermanage where id='{$post['firendID']}'";
        $rows = $db->fetchOne($sql,$link);
        $friendsListNew = explode(",",$rows['friends']);
        foreach($friendsListNew as $k=>$v){
            if($v == $post['id']){
              unset($friendsListNew[$k]);
            }
        }
        $arrListNew = '';
        if($friendsListNew){
            $arrListNew = implode(",", $friendsListNew);
        }
        $sql="update usermanage set friends = '{$arrListNew}' where id='{$post['firendID']}'";
        $db->update($link,$sql);


        $arrList = '';
        if($friendsList){
            $arrList = implode(",", $friendsList);
        }
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
                $data['errcode'] = "他已经是你好友";
                echo $db->encodeJson($data);
                return false;
            }
        }
        //好友列表添加自己
        $sql="select * from usermanage where id='{$new_id}'";
        $rowNew = $db->fetchOne($sql,$link);
        $friendsListNew = explode(",",$rowNew['friends']);
        foreach($friendsListNew as $k=>$v){
            if($v == $post['id']){
                $data['errcode'] = "你已经是他好友";
                echo $db->encodeJson($data);
                return false;
            }
        }
        if($friendsListNew[0] == ''){
            $arrListNew = $post['id'];
        }else{
            array_push($friendsListNew,$post['id']);
            $arrListNew = '';
            $arrListNew = implode(",", $friendsListNew);
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

        $sql="update usermanage set friends = '{$arrListNew}' where id='{$new_id}'";
        $db->update($link,$sql);

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