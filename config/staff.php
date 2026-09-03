<?php

return [
    'emails' => array_values(array_filter(array_map(
        trim(...),
        explode(',', (string) env('STAFF_EMAILS', '')),
    ))),
];
