<?php

// Regexp
$regexp = Array(
    'date' => "/^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$/",
    'amount' => "/^[-]?[0-9]+\$/",
    'number' => "/^[-]?[0-9,]+\$/",
    'alfanum' => "/^[0-9a-zA-Z ,.-_\\s\?\!]+\$/",
    'not_empty' => "[a-z0-9A-Z]+",
    'words' => "/^[A-Za-z]+[A-Za-z \\s]*\$/",
    'phone' => "/^[0-9]{10,11}\$/",
    'zipcode' => "/^[1-9][0-9]{3}[a-zA-Z]{2}\$/",
    'plate' => "/^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$/",
    'price' => "/^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$/",
    '2digitopt' => "/^\d+(\,\d{2})?\$/",
    '2digitforce' => "/^\d+\,\d\d\$/",
    'anything' => "/^[\d\D]{1,}\$/",
    'uri' => "/^[0-9a-zA-Z_]+$/"
);

function validate_regex ($data, $regexp)
{
    if (filter_var($data, FILTER_VALIDATE_REGEXP, $regexp)) {
        // echo 'validation: pass: ' . $data;
        return true;
    } else {
        // echo 'validation: fail: ' . $data;
        return false;
    }
}

function sanitize_data ($data) {

}