<?php
return [
    'indices' => [
        'hse2data' => [
            'index' => 'hse2data',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        "char_filter" => [
                            "c_replace_nbsp_empty" => [
                                "type" => "pattern_replace",
                                "pattern" => "&nbsp;",
                                "replacement" => ""
                            ]
                        ],
                        'analyzer' => [
                            'a_search_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_trim_empty']
                            ],
                            'a_search_pinyin_single' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['f_single_pinyin', 'f_synonyms','f_trim_empty']
                            ],
                            'a_search_pinyin_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_word_pinyin', 'f_synonyms','f_trim_empty']
                            ],
                            'a_ik_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_synonyms','f_trim_empty'],
                                'char_filter' => ['c_replace_nbsp_empty']
                            ],
                            'a_ik_pinyin_words' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',
                                'filter' => ['f_word_pinyin','f_synonyms','f_trim_empty'],
                                'char_filter' => ['c_replace_nbsp_empty']
                            ],
                            'a_ik_pinyin_single' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['f_word_pinyin','f_synonyms','f_trim_empty'],
                                'char_filter' => ['c_replace_nbsp_empty']
                            ],
                        ],
                        'tokenizer' => [
                            't_ik_pinyin' => [
                                'type' => 'pinyin',
                                "remove_duplicated_term" => false,
                                "keep_first_letter" => false, // 刘德华>ldh
                                // "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                // "none_chinese_pinyin_tokenize" => true,
                                // "keep_original" => true,
                                "keep_joined_full_pinyin" => false,
                                // "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                // "keep_none_chinese_in_first_letter" => false
                            ],
                            't_ik_pinyin_no_join' => [
                                'type' => 'pinyin',
                                "remove_duplicated_term" => false,
                                "keep_first_letter" => false, // 刘德华>ldh
                                // "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                // "none_chinese_pinyin_tokenize" => true,
                                "keep_original" => false,
                                "keep_joined_full_pinyin" => false,
                                // "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                // "keep_none_chinese_in_first_letter" => false
                            ]
                        ],
                        "filter" => [
                            "f_synonyms" => [
                                "type" => "synonym",
                                "synonyms" => [
                                    "ehs,e hs, hse,h se=>e hs,h se",
                                ]
                            ],
                            'f_trim_empty' => [
                                'type' => 'length',
                                'min' => 1
                            ],
                            "f_single_pinyin"  => [
                                "type" => "pinyin",
                                "remove_duplicated_term" => true,
                                "keep_first_letter" => false, // 刘德华>ldh
                                "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => true,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                "none_chinese_pinyin_tokenize" => true,
                                "keep_original" => false,
                                "keep_joined_full_pinyin" => false,
                                "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                "keep_none_chinese_in_first_letter" => false
                            ],
                            "f_word_pinyin"  => [
                                "type" => "pinyin",
                                "remove_duplicated_term" => true,
                                "keep_first_letter" => false, // 刘德华>ldh
                                "keep_separate_first_letter" => false, //刘德华>l,d,h
                                "keep_full_pinyin" => false,
                                "keep_none_chinese" => true,
                                "keep_none_chinese_together" => true,
                                "none_chinese_pinyin_tokenize" => false,
                                "keep_original" => false,
                                "keep_joined_full_pinyin" => true,
                                "limit_first_letter_length" => 16,
                                "lowercase" => true,
                                "trim_whitespace" => true,
                                "keep_none_chinese_in_first_letter" => false
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'mappings' =>[
        'discuss' => [
            'properties' => [
                'uid' => [
                    'type' => 'integer',
                    'index' => 'not_analyzed'
                ],
                'is_delete' => [
                    'type' => 'integer',
                    'index' => 'not_analyzed'
                ],
                'view_count' => [
                    'type' => 'integer',
                    'index' => 'not_analyzed'
                ],
                'comment_count' => [
                    'type' => 'integer',
                    'index' => 'not_analyzed'
                ],
                'popularity_count' => [
                    'type' => 'float',
                    'index' => 'not_analyzed'
                ],
                'created_time' => [
                    'type' => 'integer',
                    'index' => 'not_analyzed'
                ],
                'uname' => [
                    'type' => 'string',
                    'analyzer' => 'a_ik_words',
                    // 'term_vector' => 'with_positions_offsets',
                    'index_options' => 'positions',
                    'fields' => [
                        'pinyin' => [
                            'type' => 'string',
                            'analyzer' => 'a_ik_pinyin_words',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                        ],
                        'pinyin_single' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_single'
                        ],
                    ]
                ],
                'unickname' => [
                    'type' => 'string',
                    // 'term_vector' => 'with_positions_offsets',
                    'index_options' => 'positions',
                    'analyzer' => 'a_ik_words',
                    'fields' => [
                        'pinyin' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_words'
                        ],
                        'pinyin_single' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_single'
                        ],
                    ]
                ],
                'title' => [
                    'type' => 'string',
                    // 'term_vector' => 'with_positions_offsets',
                    'index_options' => 'positions',
                    'analyzer' => 'a_ik_words',
                    'fields' => [
                        'pinyin' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_words'
                        ],
                        'pinyin_single' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_single'
                        ],
                    ]
                ],
                'content' => [
                    'type' => 'string',
                    // 'term_vector' => 'with_positions_offsets',
                    'index_options' => 'positions',
                    'analyzer' => 'a_ik_words',
                    'fields' => [
                        'pinyin' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_words'
                        ],
                        'pinyin_single' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_single'
                        ],
                    ]
                ],
                'reply_content' => [
                    'type' => 'string',
                    // 'term_vector' => 'with_positions_offsets',
                    'index_options' => 'positions',
                    'analyzer' => 'a_ik_words',
                    'fields' => [
                        'pinyin' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_words'
                        ],
                        'pinyin_single' => [
                            'type' => 'string',
                            // 'term_vector' => 'with_positions_offsets',
                            'index_options' => 'positions',
                            'analyzer' => 'a_ik_pinyin_single'
                        ],
                    ]
                ]
            ]
        ],
    ]





];
