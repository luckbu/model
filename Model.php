<?php
namespace luckbu\model;
class Model{
    private static $config;
    public static function __callStatic($name, $arguments)
    {
        return self::parseAction($name,$arguments);
    }
    public function __call($name, $arguments)
    {
        return self::parseAction($name,$arguments);
    }
    private static function parseAction($name, $arguments){
        //system\model\Article
        //获得调用Model这个类的类的名字
        $table = get_called_class();
        //将上面的路径分割后得到表名
        $table = strtolower(ltrim(strrchr($table,'\\'),'\\'));
//        *************
//        p($table);
//        article
//        ***************
//        $pdo = new Base(self::$config,$table);
//        $pdo->get();
        //把config里面和$name(get()方法）传到base里
        return call_user_func_array([new Base(self::$config,$table),$name],$arguments);
    }
        public static function setConfig($config)
        {
            //
            self::$config = $config;
        }

}