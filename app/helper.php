<?php
use Illuminate\Support\Collection;

function toFloat($string)
{
    return floatval(trim($string));
}

/**
 * @param $store
 * @return Collection
 */
function loadStorage($store)
{
    global $storage_prefix; // uses for testing
    $storage_path = storage_path("/{$storage_prefix}{$store}.json");
    return collect((array)@json_decode(file_get_contents($storage_path), true));
}

/**
 * @param $store string
 * @param $collection Collection
 * @return false|int
 */
function saveStorage($store, $collection)
{
    global $storage_prefix; // uses for testing
    $storage_path = storage_path("/{$storage_prefix}{$store}.json");
    return file_put_contents($storage_path, $collection->toJson());
}
