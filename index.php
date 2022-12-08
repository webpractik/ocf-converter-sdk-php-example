<?php
require_once(__DIR__ . '/vendor/autoload.php');

$apiKeyOption = getopt(null, ['apiKey:']);
$apiKey       = $apiKeyOption ? $apiKeyOption['apiKey'] : null;
if (!$apiKey) {
    die('--apiKey обязателен' . PHP_EOL);
}

$filePathOption = getopt(null, ['filePath:']);
$filePath       = $filePathOption ? $filePathOption['filePath'] : null;
if (!$filePath) {
    die('--filePath обязателен' . PHP_EOL);
}

$toOption = getopt(null, ['to:']);
$to       = $toOption ? $toOption['to'] : null;
if (!$to) {
    die('--to обязателен' . PHP_EOL);
}

$client = new Webpractik\OcfConverter\Sdk\OcfClient($apiKey);

$extensionToConvertTo = $to;

try {
    $task = $client->uploadFile($filePath, $extensionToConvertTo);

    $result = $task->waitForConversion();

    if ($result->isSuccess()) {
        $resultUrl = $result->getResultingFileUrl();

        $resultFileName = "result.$to";

        if (file_put_contents($resultFileName, file_get_contents($resultUrl))) {
            $result->deleteFile();

            echo 'Готово' . PHP_EOL;
        } else {
            echo 'Не удалось сохранить результат конвертации локально' . PHP_EOL;
        }
    } else {
        echo 'Конвертация прошла неудачно' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Exception: ', $e->getMessage(), PHP_EOL;
}
