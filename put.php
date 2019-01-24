<?php
/**
 * Created by PhpStorm.
 * User: b_tt
 * Date: 19/1/23
 * Time: 16:49
 */
require_once 'QueueUtil.php';


/*require_once 'vendor/autoload.php';

use Pheanstalk\Pheanstalk;

//创建一个Pheanstalk对象
$p = new Pheanstalk('127.0.0.1', 11301);

$data = array(
	'id' => 1,
	'name' => 'test',
	'method' => 'out',
	'params' => ['uId' => 12003, 'gender' => 1],
);

//向userReg管道中添加任务，返回任务ID
//put()方法有四个参数
//第一个任务的数据
//第二个任务的优先级，值越小，越先处理
//第三个任务的延迟
//第四个任务的ttr超时时间
$id = $p->useTube('userReg')->put(json_encode($data));
//获取任务
$job = $p->peek($id);
//查看任务状态
print_r($p->statsJob($job));*/

// 加入队列

QueueUtil::loadJob('addlog', ['data' => 1 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 2 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 3 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('sendSMS', ['phone' => 17611629667, 'msg' => 'msg~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 4 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 5 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 6 . '~~~~~~~~~~~~']);
