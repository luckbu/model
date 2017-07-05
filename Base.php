<?php
namespace luckbu\model;

use PDO;
use PDOException;

class Base{
    private static $pdo = NULL;
    private $table;
    private $where = '';

    //加载后自动连接数据库 $config是配置文件被传上来
    public function __construct($config,$table) {
        //进入方法后自动运行连接数据库的方法
        $this->connect($config);

        //将传过来的数据存到$this->table里
        $this->table = $table;
    }


     //链接数据库

    private function connect($config){
        //如果属性$pdo已经链接过数据库了，不需要重复链接了 return直接停止
        if(!is_null(self::$pdo)) return;
        try{
            $dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'];
            $user = $config['db_user'];
            $password = $config['db_password'];
            $pdo = new PDO($dsn,$user,$password);
            //设置错误
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //设置字符集
            $pdo->query("SET NAMES " . $config['db_charset']);
            //存到静态属性中
            self::$pdo = $pdo;

        }catch (PDOException $e){
            exit($e->getMessage());
        }
    }
    //条件方法
    public function where($where){ //aid=3
        $this->where = " WHERE {$where}";
        //p($where);
        return $this;
    }


    // 获取数据
    public function get(){
        $sql = "SELECT * FROM {$this->table} {$this->where}";
        return $this->q($sql);
    }
    //查找数据
    public function find($pri){ //目前$pri为3
        //获得主键字段，比如cid还是aid
        //如果是Article::find(4)，那么现在$priField它是aid
        $priField = $this->getPri();
        //经过$this->where方法之后，那么$this->where的值是 WHERE aid=4
        //  getPri()
        $this->where("{$priField}={$pri}"); // " WHERE aid=3"

        $sql = "SELECT * FROM {$this->table} {$this->where}";  //
//		echo $sql;
        $data = $this->q($sql);
		//p($data);
        //把原来的二维数组变为一维数组
        $data = current($data);
		//p($data);
        $this->data = $data;//***这里存入属性里是为了下面调用，
//        p($this->data);
        return $this;//返回一个对象
    }
    //根据主键去查找信息
    public function findArray($pri){
        $obj = $this->find($pri);
        return $obj->data;
    }


    public function toArray(){
        return $this->data;
//        p($this->data);
    }


    //获得表的主键
    public function getPri(){
        //
        $desc = $this->q("DESC {$this->table}");
        //打印desc看标的结构调试
//        ******************************
//        p($desc);
//        Array
//        (
//            [0] => Array
//            (
//                [Field] => gid
//                [Type] => smallint(6)
//                [Null] => NO
//                [Key] => PRI
//                [Default] =>
//                 [Extra] => auto_increment
//        )
//
//            [1] => Array
//        (
//                [Field] => gname
//                [Type] => char(10)
//                [Null] => NO
//                [Key] =>
//                [Default] =>
//                [Extra] =>
//        )
//
//)
//        *********************************
        $priField = '';
        foreach ($desc as $v){
            if($v['Key'] == 'PRI'){
                $priField = $v['Field'];
                //p($priField);
                break;
            }
        }
        return $priField;
    }

    public function count($field='*'){//*****************************
        $sql = "SELECT count({$field}) as c FROM {$this->table} {$this->where}";
        $data = $this->q($sql);
//		p($data);
        return $data[0]['c'];
    }


    /**
     * 执行有结果集操作
     * @param $sql [sql语句]
     *
     * @return mixed
     */
    public function q($sql){
        try{
            $result = self::$pdo->query($sql);
            //p($result);
//            PDOStatement Object
//            (
//                [queryString] => SELECT * FROM article
//            )
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            //p($data);
            return $data;
        }catch (PDOException $e){
            exit($e->getMessage());
        }

    }

    /**
     * 执行无结果集操作例如：增删改
     * @param $sql
     */
    public function e($sql){
        try{
            return self::$pdo->exec($sql);

        }catch (PDOException $e){
            exit($e->getMessage());
        }
    }
    public function remove($pri){

        //组合sql语句
        $sql ="DELETE FROM {$this->table} WHERE ".$pri;
        //获得无结果集数据
        $data = $this->e($sql);
        //return $this;
        p($data);
    }














}