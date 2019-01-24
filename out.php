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


require_once 'vendor/autoload.php';

use Pheanstalk\Pheanstalk;

try {
	$beanstalk = new Pheanstalk(QueueUtil::HOST, QueueUtil::PORT);
	$tube = QueueUtil::QUEUE_TUBE;

	$beanstalk = $beanstalk->watch($tube);
	$beanstalk = $beanstalk->ignore('default');

	QueueUtil::logFile('beanstalk connected ', __FUNCTION__, __LINE__);

	while (1) {
		$job = $beanstalk->reserve();
		$_job = $job;
		$job = array_values(QueueUtil::object_to_array($job));
//		print_r(['------------------']);
//		print_r($job);
		QueueUtil::logFile($job, __FUNCTION__, __LINE__);
		$jobId = $job[0];
		echo $jobId . PHP_EOL;
		$jobBody = json_decode($job[1], 1);
		$method = $jobBody['consumer'];
		$params = $jobBody['params'];
		if (method_exists(QueueUtil::class, $method)) {
			$result = QueueUtil::$method($params);
			QueueUtil::logFile($method . ' result: ' . $result . PHP_EOL, __FUNCTION__, __LINE__);
			$beanstalk->delete($_job);
		} else {
			QueueUtil::logFile(' QueueUtil 中没找到方法 ' . $method, __FUNCTION__, __LINE__);
			$beanstalk->delete($_job);
		}
		sleep(1);
	}

} catch (Exception $ex) {
	$msg = $ex->getMessage();
	QueueUtil::logFile($msg, __FUNCTION__, __LINE__);
}