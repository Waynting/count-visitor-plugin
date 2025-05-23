<?php
/*
Plugin Name: 每日訪客計數器
Description: 顯示每天的訪客數，可用於小工具與短代碼 [daily_visitors]。
Version: 1.1
Author: Wayn Liu
*/

class Daily_Visitor_Counter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'daily_visitor_counter_widget',
            '每日訪客計數器',
            ['description' => '顯示今天的訪客數']
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . '訪客統計' . $args['after_title'];
        echo '<div style="font-size: 16px; text-align: center;">' . daily_visitors_html() . '</div>';
        echo $args['after_widget'];
    }
}

function register_daily_visitor_counter_widget() {
    register_widget('Daily_Visitor_Counter_Widget');
}
add_action('widgets_init', 'register_daily_visitor_counter_widget');

// === 共用邏輯 ===
function daily_visitors_html() {
    $upload_dir = wp_upload_dir();
    $filename = $upload_dir['basedir'] . '/visitor_counter.txt';
    $today = date("Y-m-d");
    $count = 0;
    $data = [];

    $fp = fopen($filename, "c+");
    if (flock($fp, LOCK_EX)) {
        $fileContent = stream_get_contents($fp);
        $data = !empty($fileContent) ? json_decode($fileContent, true) : [];

        if (isset($data['date']) && $data['date'] === $today) {
            $count = (int)$data['count'] + 1;
        } else {
            $count = 1;
        }

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(['date' => $today, 'count' => $count]));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);

    return "你是今天的第 <strong>$count</strong> 位訪客！";
}

// === 短代碼 [daily_visitors] ===
function daily_visitors_shortcode() {
    return '<div style="font-size: 18px; text-align: center;">' . daily_visitors_html() . '</div>';
}
add_shortcode('daily_visitors', 'daily_visitors_shortcode');
