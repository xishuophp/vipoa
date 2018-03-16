<?php
namespace common\models;
use Yii;
use common\models\RedisDB;


class RedisQueue extends RedisDB{

    protected $_quit_time=0;//阻塞队列超时时间，无限时
    private   $_queue_prefix='queue_';

    /**
     * 在指定队列中添加任务
     * @param array $params
     * @param string $queue
     * @return NULL|string|number
     */
    public function putin($params,$queue='default'){
        if(empty($params)){
            return null;
        }
        
        if(!$this->exists($queue)){
            Yii::error("redisQueue [queue=$queue] is not exist");
            return null;
        }
        //var_dump($params);
        return $this->add($queue,$params);
    }
    
    /**
     * 获取队列的任务，队列为空时阻塞quit_time定义的时间后退出
     *
     * @param string $queue         
     * @return string
     */
    public function getit($queue='default'){
        if(!$this->exists($queue)){
            Yii::error("RedisQueue [queue=$queue] is not exist");
            return null;
        }
        try{
            $result=$this->get($queue);
            if(!empty($result)){
                return json_decode($result[1],true);
            }else{
                return null;
            }
        }catch(\Exception $e){
            Yii::error('get queue data syntax error,errorInfo='.$e->getMessage());
            return null;
        }
    }
    
    
    /**
     * 返回各个队列中的任务总数
     * @param string $queue
     * @return string
     */
    public function length($queue='default') {
        $queueName = $this->getQueuename($queue);
        if(in_array($queueName, Yii::$app->params['redisQueue'])){
            $redisConnect = $this->getRedisConnect($queueName);
            return $redisConnect->LLEN($queueName);
        }
        return 0;
    }
    
    
    /**
     * 
     * 检查queue名是否已经定义
     * @param unknown $queue
     * @return boolean
     */
    public function exists($queue){
        if(!in_array($this->getQueuename($queue),Yii::$app->params['redisQueue'])){
            return false;
        }
        return true;
    }

    
    /**
     * 返回队列名称
     * @param string $queue
     * @return string
     */
    private function getQueuename($queue){
        return $this->_queue_prefix.strtolower($queue);
    } 
    
    /**
     * 往队列中添加一个任务
     * @param string $queue
     * @param array $params
     * @return int
     */
    private function add($queue,$params){
        $queueName = $this->getQueuename($queue);
        $redisConnect = $this->getRedisConnect($queueName);
        $result=$redisConnect->LPUSH($queueName,json_encode($params));
        // 如果定义了队列的limit，修剪队列不超过最大值(ltrim)
        return $result;
    }
    
    /**
     * 从队列中获取一个任务
     * @param string $queue
     * @return array
     */
    private function get($queue){
        $queueName = $this->getQueuename($queue);
        $redisConnect = $this->getRedisConnect($queueName);
        $result=$redisConnect->BRPOP($queueName,$this->_quit_time);
        return $result;
    }
}
