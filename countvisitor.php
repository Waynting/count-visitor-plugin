<?php
$filename = "counter.txt";
$count = 0;

if (file_exists($filename)) {
    $count = (int)file_get_contents($filename);
}
$count++;
file_put_contents($filename, $count);

echo "你是今天的第 $count 位訪客！";
?>