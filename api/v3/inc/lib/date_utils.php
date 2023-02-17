<?php

function current_zulu_time(): string
{
    return DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d\TH:i:s.v\Z');
}