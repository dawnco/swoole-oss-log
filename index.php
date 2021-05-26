<?php
/**
 * @author Dawnc
 * @date   2021-02-23
 */

include __DIR__ . "/vendor/autoload.php";

Swoole\Runtime::enableCoroutine();

go(function () {
    \App\Lib\Aliyun\AliTest::test();
});
