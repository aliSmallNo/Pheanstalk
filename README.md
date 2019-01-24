# Pheanstalk
	参考文档 https://www.kancloud.cn/daiji/beanstalkd/735176
	Beanstalk，一个高性能、轻量级的分布式内存队列系统，最初设计的目的是想通过后台异步执行耗时的任务来降低高容量Web应用系统的页面访问延迟，支持过有9.5 million用户的Facebook Causes应用。

##### 一、Beanstalkd是什么？

	Beanstalkd是一个高性能，轻量级的分布式内存队列

##### 二、Beanstalkd特性
	1、支持优先级(支持任务插队)
	2、延迟(实现定时任务)
	3、持久化(定时把内存中的数据刷到binlog日志)
	4、预留(把任务设置成预留，消费者无法取出任务，等某个合适时机再拿出来处理)
	5、任务超时重发(消费者必须在指定时间内处理任务，如果没有则认为任务失败，重新进入队列)

##### 三、Beanstalkd核心元素
	生产者 -> 管道(tube) -> 任务(job) -> 消费者
	Beanstalkd可以创建多个管道，管道里面存了很多任务，消费者从管道中取出任务进行处理。

##### 四、任务job状态
	delayed 延迟状态
	ready 准备好状态
	reserved 消费者把任务读出来，处理时
	buried 预留状态
	delete 删除状态



## 安装Pheanstalk
#### Linux centos上安装
#####    git 下载
	git clone https://github.com/beanstalkd/beanstalkd.git
#####    安装
	cd beanstalkd
	make
	
#####    运行
	./beanstalkd -l 127.0.0.1 -p 11301
	-l 指定ip
	-p 指定端口
    		
#### 其他安装方式
	OS X
  	brew install beanstalkd    




## 开始使用

##### 在项目中新建 composer.json 文件

	{
		"require" : {
			"pda/pheanstalk" : "^3.1"
		}
	}
##### 在控制台执行（当前项目目录，composer.json目录）下执行
    
	composer install

##### 开始使用
	<?php
	require_once 'vendor/autoload.php';
	use Pheanstalk\Pheanstalk;
	$p = new Pheanstalk('127.0.0.1', 11301);
	//查看beanstalkd当前的状态信息
	var_dump($p->stats());
    
##### 查看beanstalkd当前的状态信息
	var_dump($p->stats());

##### 目前存在的管道
	var_dump($p->listTubes());
    
##### 目前监听的管道
	var_dump($p->listTubesWatched());
		
##### 管道的状态
	var_dump($p->statsTube('default'));
		
##### 指定使用的管道
	var_dump($p->useTube('aaaaa'));
		
##### 查看任务的详细信息
	var_dump($p->statsJob());

##### 通过任务ID获取任务
	var_dump($p->peek());
		
		
		
		
		
## 生产者示例
	<?php
	require_once 'vendor/autoload.php';
	
	use Pheanstalk\Pheanstalk;
	
	//创建一个Pheanstalk对象
	$p = new Pheanstalk('127.0.0.1', 11301);
	
	$data = array(
			'id' => 1,
			'name' => 'test',
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
	print_r($p->statsJob($job));
		
		
## 消费者示例
	<?php
	require_once 'vendor/autoload.php';
	
	use Pheanstalk\Pheanstalk;
	
	//创建一个Pheanstalk对象
	$p = new Pheanstalk('127.0.0.1', 11301);
	
	//监听userReg管道，忽略default管道
	$job = $p->watch('userReg')->ignore('default')->reserve();
	
	$data = json_decode($job->getData());
	//打印任务中的数据
	print_r($data);
	
	//最后删除任务，表示任务处理完成
	$p->delete($job);


## Pheanstalk方法

##### 维护方法
	stats() 查看状态方法
	listTubes() 目前存在的管道
	listTubesWatched() 目前监听的管道
	statsTube() 管道的状态
	useTube() 指定使用的管道
	statsJob() 查看任务的详细信息
	peek() 通过任务ID获取任务
		
##### 生产者方法
	putInTube() 往管道中写入数据
	put() 配合useTube()使用

##### 消费者方法
	watch() 监听管道，可以同时监听多个管道
	ignore() 不监听管道
	reserve() 以阻塞方式监听管道，获取任务
	reserveFromTube()
	release() 把任务重新放回管道
	bury() 把任务预留
	peekBuried() 把预留任务读取出来
	kickJob() 把buried状态的任务设置成ready
	kick() 批量把buried状态的任务设置成ready
	peekReady() 把准备好的任务读取出来
	peekDelayed() 把延迟的任务读取出来
	pauseTube() 给管道设置延迟
	resumeTube() 取消管道延迟
	touch() 让任务重新计算ttr时间，给任务续命
		
		
## Beanstalkd中文协议
	https://github.com/beanstalkd/beanstalkd/blob/master/doc/protocol.zh-CN.md	
		
## beanstalkd启动设置
#### 概要
	beanstalkd []

#### 描述
	Beanstalkd是一个简单的工作队列服务。它的界面是
	通用的，尽管它最初是为了
	通过
	异步运行耗时的任务来减少高容量Web应用程序中页面视图的延迟而设计的。
	
	启动时，beanstalkd打开一个套接字（或使用
	init（1）系统提供的文件描述符，参见[ENVIRONMENT] []）并监听
	传入的连接。对于每个连接，它读取一系列
	命令来创建，保留，删除和操作“作业”，
	即要完成的工作单元。参见文件doc/protocol.txt中
	beanstalkd分布的含义作充分描述
	的和格式beanstalkd协议。

#### OPTIONS
	-b：
	使用binlog将作业保留在目录中的持久存储上。
	在启动时，beanstalkd将恢复任何二进制日志是存在
	的，那么，在正常运行期间，追加新的就业机会，并
	在状态变化到二进制日志。
	
	-c：
	执行binlog文件的在线，增量压缩。否定
	-n。这是默认行为。
	
	（请不要使用此选项，除了否定-n两者。-c而且-n
	很可能会在将来被移除beanstalkd释放。）
	
	-f：
	每毫秒最多调用一次fsync（2）。更大的值
	可减少磁盘活动并以
	安全为代价提高速度。电源故障可能导致丢失高达
	毫秒的历史记录。
	
	一个0值将导致beanstalkd每次调用FSYNC 
	其写入二进制日志。
	
	（没有这个选项没有效果-b。）
	
	-F：
	永远不要调用fsync（2）。相当于-f无限的价值。
	
	这是默认行为。
	
	（没有这个选项没有效果-b。）
	
	-h：
	显示简短的帮助消息并退出。
	
	-l：
	侦听地址（默认为0.0.0.0）。
	
	（-l如果
	正在使用sd-daemon（5）套接字激活，则选项无效。另请参阅[ENVIRONMENT] []。）
	
	-n：
	关闭binlog压缩，否定-c。
	
	（请不要使用此选项两者。-c而且-n很可能会被删除
	在将来的beanstalkd版本。）
	
	-p：
	侦听TCP端口（默认为11300）。
	
	（-p如果
	正在使用sd-daemon（5）套接字激活，则选项无效。另请参阅[ENVIRONMENT] []。）
	
	-s：
	每个binlog文件的大小（以字节为单位）。
	
	（没有这个选项没有效果-b。）
	
	-u：
	成为用户及其主要组。
	
	-V：
	增加冗长度。可以多次使用以产生更
	详细的输出。输出格式可能会发生变化。
	
	-v：
	打印版本字符串并退出。
	
	-z：作业
	的最大大小（以字节为单位）。

#### 环境
	LISTEN_PID，LISTEN_FDS：
	这些变量可以通过init（1）设置。有关
	详细信息，请参阅sd_listen_fds（3）。
	也可以看看
	sd-daemon（5），sd_listen_fds（5）
	
	文件README以及doc/protocol.txt在beanstalkd
	分布。
	
	http://kr.github.com/beanstalkd/