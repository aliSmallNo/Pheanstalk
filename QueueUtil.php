<?php
/**
 * 执行队列任务
 * Created by PhpStorm.
 * User: b_tt
 * Date: 19/1/23
 * Time: 17:16
 */
require_once 'vendor/autoload.php';

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

class QueueUtil
{
	const QUEUE_TUBE = 'imei';
	const QUEUE_TUBE_SMS = 'sms_imei';

	const HOST = '127.0.0.1';
	const PORT = 11301;

	public static function loadJob($methodName, $params = [], $tube = '', $delay = 0, $priority = '', $ttr = '')
	{
		if (!$tube) {
			$tube = self::QUEUE_TUBE;
		}
		if (!$priority) {
			$priority = PheanstalkInterface::DEFAULT_PRIORITY;
		}
		if (!$ttr) {
			$ttr = PheanstalkInterface::DEFAULT_TTR;
		}

		try {
			$beanstalk = new Pheanstalk(self::HOST, self::PORT);
			$message = [
				'consumer' => $methodName,
				'params' => $params
			];
			//向管道中添加任务，返回任务ID
			//put()方法有四个参数
			//第一个任务的数据
			//第二个任务的优先级，值越小，越先处理
			//第三个任务的延迟
			//第四个任务的ttr超时时间
			$id = $beanstalk->useTube($tube)->put(
				json_encode($message),
				$priority,
				$delay,
				$ttr
			);
			if (!$id) {
				throw new Exception('发送失败');
			}
			self::logFile($message, __FUNCTION__, __LINE__, $tube);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
			self::logFile($msg, __FUNCTION__, __LINE__, $tube);
		}
	}

	public static function logFile($msg, $funcName = '', $line = '', $tube = '')
	{
		if (is_array($msg)) {
			$msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
		}
		if (is_object($msg)) {
			$msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
		}

		$msg = $funcName . ' ' . $line . ': ' . $msg;
		$msg = 'message: ' . $msg;

		$fileName = './logs/' . 'queue_' . $tube . date('Ymd') . '.log';
		@file_put_contents($fileName, date('Y-m-d H:i:s') . ' ' . $msg . PHP_EOL, FILE_APPEND);
	}

	public static function addlog($params)
	{
		self::logFile($params, __FUNCTION__, __LINE__);
	}

	public static function sendSMS($params)
	{
		self::smsMessage($params['phone'], $params['msg'],
			isset($params['rnd']) ? $params['rnd'] : rand(101, 109),
			isset($params['type']) ? $params['type'] : 'sale');
		return true;
	}


	/**
	 * 发送短信信息
	 * @param array $params
	 * @return boolean
	 * */
	public static function message($params)
	{
		self::smsMessage($params['phone'], '验证码 ' . $params['code'] . '，如非本人操作，请忽略本短信。', '100001');
		return true;
	}

	protected static function smsMessage($phone, $msg, $appendId = '1234', $type = 'sale')
	{
		$formatMsg = $msg;
		if (mb_strpos($msg, '【千寻恋恋】') == false) {
			$formatMsg = '【千寻恋恋】' . $msg;
		}
		self::logFile([$phone, $formatMsg], __FUNCTION__, __LINE__);
		return true;
	}

}
