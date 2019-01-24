<?php
/**
 * Created by PhpStorm.
 * User: b_tt
 * Date: 19/1/23
 * Time: 16:52
 */
require_once 'QueueUtil.php';

/*require_once 'vendor/autoload.php';

use Pheanstalk\Pheanstalk;

//创建一个Pheanstalk对象
$p = new Pheanstalk('127.0.0.1', 11301);

//监听userReg管道，忽略default管道
$job = $p->watch('userReg')->ignore('default')->reserve();

$data = json_decode($job->getData(), 1);
//打印任务中的数据
print_r($data);

function out($params)
{
	print_r($params);
}

$method = isset($data['method']) ? $data['method'] : '';

echo '$method：' . $method . PHP_EOL;

if ($method && function_exists($method)) {
	$method($data['params']);
}

//最后删除任务，表示任务处理完成
$p->delete($job);*/


use Pheanstalk\Pheanstalk;

// /usr/local/Cellar/php@7.1/7.1.8_20/bin/php out.php imei
$tubes = $argv;
if (isset($tubes[1])) {
	$tube = $tubes[1];
}

try {
	$beanstalk = new Pheanstalk(QueueUtil::HOST, QueueUtil::PORT);

	if (!in_array($tube, [QueueUtil::QUEUE_TUBE, QueueUtil::QUEUE_TUBE_SMS])) {
		$tube = QueueUtil::QUEUE_TUBE;
	}

	$beanstalk = $beanstalk->watch($tube);
	$beanstalk = $beanstalk->ignore('default');

	QueueUtil::logFile('beanstalk connected ', __FUNCTION__, __LINE__);

	while (1) {
		$job = $beanstalk->reserve();

		$jobId = $job->getId();
		$jobBody = json_decode($job->getData(), 1);
		echo $jobId . PHP_EOL;
		QueueUtil::logFile([$jobId, $job->getData()], __FUNCTION__, __LINE__);

		$method = $jobBody['consumer'];
		$params = $jobBody['params'];
		if (method_exists(QueueUtil::class, $method)) {
			$result = QueueUtil::$method($params);
			QueueUtil::logFile($method . ' result: ' . $result . PHP_EOL, __FUNCTION__, __LINE__);
			$beanstalk->delete($job);
		} else {
			QueueUtil::logFile(' QueueUtil 中没找到方法 ' . $method, __FUNCTION__, __LINE__);
			$beanstalk->delete($job);
		}
		sleep(1);
	}

} catch (Exception $ex) {
	$msg = $ex->getMessage();
	QueueUtil::logFile($msg, __FUNCTION__, __LINE__);
}
