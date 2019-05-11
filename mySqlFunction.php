<?php
 
class DB
{
    /** 
     * 连接MYSQL函数 
     * 连接MYSQL函数,通过常量的形式来连接数据库 
     * 自定义配置文件，配置文件中自定义常量，包含需要使用的信息 
     * @return resource 
     */
    function connect()
    {   
        //连接mysql  
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die('数据库连接失败<br/>ERROR ' . mysqli_error('error') . ':' . mysqli_error('error'));  
        //设置字符集  
        mysqli_set_charset($link, DB_CHARSET);  
        //打开指定的数据库  
        mysqli_select_db($link, DB_DBNAME) or die('指定的数据库打开失败');
        return $link;
    }
 
    /** 
     * 插入一条记录 
     * @param string $sql 
     * @param obj $link
     * @return string 
     */
    function insert($link, $sql)
    {
        if (mysqli_query($link, $sql)) {
           return true;
        } else {
            return false;
        }
    }
 
    /** 
     * 更新一条记录 
     * @param string $sql 
     * @param obj $link
     * @return string 
     */
    function update($link, $sql)
    {
        if (mysqli_query($link, $sql)) {
            return true;
        } else {
            return false;
        }
    }

    /** 
     * 删除一条记录 
     * @param string $sql 
     * @param obj $link
     * @return string 
     */
    function delete($link, $sql)
    {
        if (mysqli_query($link, $sql)) {
            return true;
        } else {
            return false;
        }
    }
 
    /** 
     * 查询一条记录 
     * @param string $sql 
     * @param string $result_type 
     * @param obj $link 
     * @return string|boolean 
     */
    function fetchOne($sql, $link, $result_type = MYSQLI_ASSOC)
    {
        $result = mysqli_query($link, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_array($result, $result_type);
        } else {
            return false;
        }
    }
 
    /** 
     * 得到表中的所有记录 
     * @param string $sql 
     * @param string $result_type 
     * @param obj $link 
     * @return array|boolean
     */
    function fetchAll($sql, $link, $flag = null,$result_type = MYSQLI_ASSOC)
    {
        $result = mysqli_query($link, $sql);
        // if(!is_null($flag)){
        //     $result['user_name'] = $flag;
        // }
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result, $result_type)) {
                if(!is_null($flag)){
                    $row['user_name'] = $flag;
                }
                $rows[] = $row;
            }
            return $rows;
        } else {
            return false;
        }
    }
 
 
    /**取得结果集中的记录的条数 
     * @param string $sql 
     * @param obj $link 
     * @return number|boolean 
     */
    function getTotalRows($sql, $link)
    {
        $result = mysqli_query($link, $sql);
        if ($result) {
            return mysqli_num_rows($result);
        } else {
            return false;
        }
    }
    /** 
     * @param string kson
     */
    function encodeJson($responseData) {
        $jsonResponse = json_encode($responseData);
        return $jsonResponse;        
    }   
 
}  