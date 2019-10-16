<?php
/**
 * Created by PhpStorm.
 * User: b_tt
 * Date: 19/1/23
 * Time: 16:18
 * 开始使用
 */

require_once 'vendor/autoload.php';

use Pheanstalk\Pheanstalk;

$p = new Pheanstalk('127.0.0.1', 11301);

print_r($p->listTubes());
exit;

// 查看 beanstalkd 当前的状态信息
echo "查看 beanstalkd当前的状态信息" . PHP_EOL;
print_r($p->stats());

echo "目前存在的管道" . PHP_EOL;
print_r($p->listTubes());

echo "目前监听的管道" . PHP_EOL;
print_r($p->listTubesWatched());

echo "管道的状态" . PHP_EOL;
print_r($p->statsTube('default'));

echo "指定使用的管道" . PHP_EOL;
print_r($p->useTube('aaaaa'));

echo "查看任务的详细信息" . PHP_EOL;
//print_r($p->statsJob());

echo "通过任务ID获取任务" . PHP_EOL;
//print_r($p->peek());
