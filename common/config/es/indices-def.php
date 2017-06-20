<?php
return [
    'bank' => [
        'index' => 'bank',
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'cn_keywords_with_pinyin' => [
                            'type' => 'custom',
                            'tokenizer' => 'ik_max_word',
                            'filter' => ["pinyin_filter" ,"unique"]
                        ]
                    ],
                    "filter" => [
                        "pinyin_filter"  => [
                            "type" => "pinyin",
                            "keep_first_letter" => false, // 刘德华>ldh
                            "keep_separate_first_letter" => true, //刘德华>l,d,h
                            "keep_full_pinyin" => true,
                            "keep_none_chinese" => true,
                            "keep_original" => true,
                            "keep_joined_full_pinyin" => true,
                            "limit_first_letter_length" => 16,
                            "lowercase" => true,
                            "trim_whitespace" => true,
                            "keep_none_chinese_in_first_letter" => true
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'bankcard' => [
                    'properties' => [
                        'value' => [
                            'type' => 'string',
                            'analyzer' => 'cn_keywords_with_pinyin'
                        ],
                        'code' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ]
                    ]
                ]
            ]
        ]
    ]




];
