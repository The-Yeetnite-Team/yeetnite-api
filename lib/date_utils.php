<?php

function current_zulu_time(): string
{
    return DateTime::createFromFormat('U.u', current_timestamp())->format('Y-m-d\TH:i:s.v\Z');
}

function zulu_time_from_timestamp(float $timestamp): string {
    return DateTime::createFromFormat('U.u', $timestamp)->format('Y-m-d\TH:i:s.v\Z');
}

function current_timestamp(): float {
    return microtime(true);
}