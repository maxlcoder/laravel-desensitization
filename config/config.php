<?php

/*
 * You can place your custom package configuration in here.
 *
 */
return [
    'functions' => [
        'mobile' => 'desensitise_mobile',
    ],
    'class' => [
        'name' => 'Xxx\Yyy\Desensitization',
        'functions' => [
            'mobile' => 'desensitiseMobile',
        ],
    ],
    'uris' => [
        'admin/family/get_list' => [
            ['key' => 'data.data.*.name', 'type' => 'real_name'],
            ['key' => 'data.data.*.mobile', 'type' => 'mobile'],
        ],
    ],
];