<?php
namespace common\models;

use Yii;
use common\models\Flexihash;

class RedisDB
{
    protected $_connectPool=null;
    protected $hash=null;
    protected $redisServers = array();

    public function __construct()
    {
        $this->redisServers = Yii::$app->params['redisServers'];

        $this->hash = new Flexihash();
        $nodes=array_keys($this->redisServers);
        $this->hash->addTargets($nodes);
    }


    /**
     * 根据key值返回返回redis连接
     * @param $key
     */
    public function getRedisConnect($key)
    {

        $node_key = $this->hash->lookup($key);
        //var_dump($node_key);
        $config = $this->redisServers[$node_key];
        return Yii::$app->$config;
    }



}