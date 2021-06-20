<?php

function sanitizeFormPassword ($inputText): string {
    $inputText = filter_var($inputText, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

    return $inputText;
}

function sanitizeFormUsername ($inputText): string {
    $inputText = trim($inputText);
    $inputText = filter_var($inputText, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

    return $inputText;
}

function sanitizeFormString ($inputText): string {
    $inputText = trim($inputText);
    $inputText = filter_var($inputText, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
    $inputText = ucfirst(strtolower($inputText));

    return $inputText;
}

