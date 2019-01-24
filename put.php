<?php
/**
 * Created by PhpStorm.
 * User: zhoupan
 * Date: 19/1/23
 * Time: 16:49
 */
require_once 'QueueUtil.php';

// 加入任务

QueueUtil::loadJob('addlog', ['data' => 1 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 2 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 3 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('sendSMS', ['phone' => 13323231212, 'msg' => 'msg~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 4 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 5 . '~~~~~~~~~~~~']);
QueueUtil::loadJob('addlog', ['data' => 6 . '~~~~~~~~~~~~']);
