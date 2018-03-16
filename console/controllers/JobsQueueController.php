<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use common\models\RedisQueue;
use common\models\RedisSchedule;

/**
 * 队列服务
 *
 */

class JobsQueueController extends Controller
{

    const QUEUE_TYPE_HTTP_POST = 'http_post';
    const QUEUE_TYPE_HTTP_GET = 'http_get';
    const QUEUE_TYPE_CLASS = 'class';

    private $redisQueue = null;
    private $redisSchedule = null;

    public function init()
    {
        parent::init();
        if($this->redisQueue === null){
            $this->redisQueue = new RedisQueue;
        }
        if($this->redisSchedule === null){
            $this->redisSchedule = new RedisSchedule;
        }
    }

    
    /**
     * 统一封装入口，执行队列任务，需要传入要执行的队列类型。任务对应的处理过程在QueueProcess定义
     * 队列随机执行10-15分钟，超时退出，等corn重启
     * 
     * @param string $qname 队列名称
     * @return null
     */
    public function actionWorker($queue='default'){
        if(!$this->redisQueue->exists($queue)){
            echo "\n".date("Y-m-d H:i:s")."---Not found the name of ".$queue." queue.\n";
            return;
        }

        // 启动一个进程，随机执行5-10分钟，防止长时间进程锁死。
        $timestamp=time();
        $quit_time=rand(5,10)*60;
        
        $i=0;
        while(true){
            if(time()-$timestamp>$quit_time){
                echo "\n"."the worker over max times {$i} or over define process time: runed {$quit_time}s\n";
                break;
            }else{
                $task=$this->redisQueue->getit($queue);
                if(!empty($task)){
                    echo '[NOTICE] '.sprintf(time()." Call %s [type=%s] [params=%s]\n",__FUNCTION__,$task['type'],json_encode($task));
                    $result=$this->queue_run($task);
                    echo '[RESULT] '.json_encode($result)."\n";
                    $i++;
                }else{
                    //echo time() ." blpop timeout,execute {$i}" . "\n";
                    break;
                }
            }
        }
    }
    
    /**
     * 执行定时队列任务
     * @param string $queue
     */
    public function actionSchedule($queue='default'){
        if(!$this->redisschedule->exists($queue)){
            echo "\n".date("Y-m-d H:i:s")."---Not found the schedule name of ".$queue.".\n";
            return;
        }

        // 启动一个进程，随机执行5-10分钟，防止长时间进程锁死。
        $timestamp=time();
        $quit_time=rand(5,10)*60;

        $i=1;
        while(true){
            if(time()-$timestamp>$quit_time){
                //echo "\n"."the worker over max times {$i} or over define process time: runed {$quit_time}s\n";
                break;
            }else{
                $tasks=$this->redisschedule->getit($queue);
                if($tasks){
                    foreach($tasks as $key=>$schedule_task){
                        if(!$schedule_task){
                            //临时兼容老队列任务，等老队列的任务执行完毕后删除此代码
                            $schedule_task = $this->redisdb->get($key);
                            $this->redisdb->del($key);
                        }

                        echo '[NOTICE] '.sprintf(time()." Schedule Task [%s] [task=%s]\n",__FUNCTION__, $schedule_task);

                        $task =json_decode($schedule_task,true);
                        
                        if(!isset($task['queue'])){
                            $task_queue = QUEUE_DEFAULT;
                        }else{
                            $task_queue = $task['queue'];
                        }
                        
                        unset($task['queue']);
                        unset($task['timestamp']);
                
                        $result=$this->redisqueue->putin($task,$task_queue);
                    }
                }
                sleep(1);
            }
        }
    }
    
    
    /**
     * 执行queue的命令
     * @param array $task
     * @return string
     */
    private function queue_run($task){
        switch($task['type']){
            case self::QUEUE_TYPE_HTTP_POST:
                if(!isset($task['url'])){
                    echo "入口url没有定义，task=".json_encode($task).PHP_EOL;
                    return;
                }
                $result=$this->run_http_post($task['url'],$task['params']);
                break;
            case self::QUEUE_TYPE_HTTP_GET:
                if(!isset($task['url'])){
                    echo "入口url没有定义，task=".json_encode($task).PHP_EOL;
                    return;
                }
                $result=$this->run_http_get($task['url'],$task['params']);
                break;
            case self::QUEUE_TYPE_CLASS:
                if(!isset($task['class'])){
                    echo "class没有定义，task=".json_encode($task).PHP_EOL;
                    return;
                }
                $result=$this->run_class($task['class'], $task['method'], $task['params']);
                break;
            default:
                $result='';
                break;
        }
        //echo json_encode($result)."\n";
        return $result;
    }
    
    
    private function run_http_post($url,$params){
        $data=http_build_query($params);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GAEA API PHP/1.0');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result=self::executeTime(3, $ch);
        if(curl_errno($ch)){
            Yii::log( 'call '.__FUNCTION__.", curl execute error. [method]:POST [url]:$url [params]:". json_encode($params),'error');
        }
        curl_close($ch);
        
        return $result;
    }
        
    private function run_http_get($url,$params){
        $data=http_build_query($params);
        if(strstr($url,'?')){
            $url.='&'.$data;
        }else{
            $url.='?'.$data;
        }
        
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GAEA API PHP/1.0');
        curl_setopt($ch, CURLOPT_POST, 0);
        $result=self::executeTime(3, $ch);
        if(curl_errno($ch)){
            Yii::log('call '.__FUNCTION__.", curl execute error. [method]:GET [url]:$url [params]:". json_encode($params),'error');
        }
        curl_close($ch);
        
        return $result;
    }
    
    /**
     * 按设定次数执行curl
     * @param int $count
     * @param object $curl
     * @return mixed
     */
    private function executeTime($count,$curl){
        $result=curl_exec($curl);
        if(curl_errno($curl)){
            echo '请求错误: '.curl_errno($curl)."\n";
            
            if($count>0){
                $count--;
                $result=self::executeTime($count,$curl);
            }
            return null;
        }
        return $result;
    }
    
    private function run_class($class,$method, $params){
        try{
            //var_dump($class, $method, $params);
            $paramsArr = [];
            foreach($params as $param){
                $paramsArr[] = $param;
            }
            call_user_func_array(array($class, $method),$paramsArr);
            
        }catch(Exception $e){
            Yii::log('call '.__FUNCTION__." . [method]:QUEUE_TYPE_CLASS [class]:$class [params]:". json_encode($params),'error');
        }
    }


}