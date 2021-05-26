<?php
/**
 * @date   2021-02-23
 */

namespace App;


use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;
use Swoole\Table;

class Application
{

    protected $connections = [];


    protected $number = 0;


    public function __construct()
    {
        $this->connections = new Table(1024);
        $this->connections->column('fd', Table::TYPE_INT);
        $this->connections->create();
    }

    public function start()
    {
        $host = "0.0.0.0";
        $port = 8999;

        $server = new Server($host, $port);

        $server->set([
            'worker_num' => 1
        ]);

        $server->on('Connect', function ($server, $fd) {
            //$this->connect($server, $fd);
        });

        $server->on('WorkerStart', function (Server $server, int $workerId) {
            Log::info('WorkerStart id: %s', $workerId);
        });

        //监听数据接收事件
        $server->on('Receive', function (Server $server, $fd, $reactor_id, $data) {

            var_dump("CID " . Coroutine::getCid());
            go(function () {
                var_dump("CID GO " . Coroutine::getCid());
            });

            Log::info('workerId %s pid %s Receive', $server->getWorkerId(), $server->getWorkerPid());

            $sleep = rand(1, 10);

            usleep($sleep);

            $int  = new Test();
            $int2 = new Test();
            //
            //var_dump($int);

            //$this->number = $this->number + $sleep * 10;

            $out = sprintf('%s %s', $int, $int2);


            $length = strlen($out);
            $server->send($fd, "HTTP/1.1 200 OK
Server: nginx/1.18.0
Date: Mon, 17 May 2021 01:50:58 GMT
Content-Type: application/html; charset=utf-8
Content-Length: $length

$out");

            //$this->receive($server, $fd, $data);
        });

        $server->on('request', function (Request $request, Response $response) {
            Log::info('request fd %s', $request->fd);
            sleep(10);
            $response->end("ok");
        });

        //监听连接关闭事件
        $server->on('Close', function ($server, $fd) {
            $this->close($server, $fd);
        });

        Log::info("start at %s:%s", $host, $port);
        //启动服务器
        $server->start();


    }

    private function connect(Server $server, $fd)
    {

        $this->connections->set($fd, ['fd' => $fd]);
        Log::info("new connect fd:%s", $fd);
    }

    private function receive(Server $server, $fd, $data)
    {

        go(function () {
            for ($i = 0; $i < 10; $i++) {
                echo $i;
                sleep(1);
            }
        });
        Log::info("fd:%s receive", $fd);
        $rand = rand(100, 999);
        foreach ($this->connections as $conn) {
            $cfd = $conn['fd'];
            Log::info("send to fd:%s", $cfd);
            $server->send($cfd, $rand . "\r\n");
        }
    }

    private function close(Server $server, $fd)
    {
        Log::info("移除链接 %s", $fd);

    }
}
