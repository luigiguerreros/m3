<?php
$messages = M3::$session->messages;

if ($messages) {
    $html_messages = '';
    foreach ($messages as $message) {
        switch ($message['type']) {
        case 'message':
            $ico = 'message'; break;
        case 'warning':
            $ico = 'warning'; break;
        case 'error':
            $ico = 'error'; break;
        default:
            $ico = 'unknow'; break;
        }

        $html_messages .= '<div class="m3_message ' . $ico . '">' . $message['message'] . "</div>\n";
    }

    echo M3\Html\Tag::div(['class'=>'m3_message_container'], $html_messages)
        ->noEscapeContent();

    // Clear the messages from the session
    M3::$session->messages = [];
}