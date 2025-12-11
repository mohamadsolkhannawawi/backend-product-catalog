<?php

return [
    'defines' => [
        /**
         * The location of the DOMPDF font directory
         *
         * @var string The location of the font directory
         */
        'font_dir' => storage_path('fonts/'),

        /**
         * The location of the DOMPDF font cache directory
         *
         * @var string The location of the font cache directory
         */
        'font_cache' => storage_path('fonts/'),

        /**
         * Whether to enable font subsetting or not.
         *
         * @var bool
         */
        'enable_font_subsetting' => false,

        /**
         * The PDF rendering backend to use
         *
         * @var string
         */
        'pdf_backend' => 'CPDF',

        /**
         * The default paper size.
         *
         * @var string
         */
        'default_paper_size' => 'a4',

        /**
         * The default font family
         *
         * @var string
         */
        'default_font' => 'Arial',

        /**
         * Enable HTML5 parser
         *
         * @var bool
         */
        'enable_html5_parser' => true,

        /**
         * Enable remote files
         *
         * @var bool
         */
        'enable_remote' => true,

        /**
         * The height of the paper
         *
         * @var float
         */
        'paper_width' => 8.5,

        /**
         * The width of the paper
         *
         * @var float
         */
        'paper_height' => 11,

        /**
         * The left margin
         *
         * @var float
         */
        'margin_left' => 15,

        /**
         * The right margin
         *
         * @var float
         */
        'margin_right' => 15,

        /**
         * The top margin
         *
         * @var float
         */
        'margin_top' => 20,

        /**
         * The bottom margin
         *
         * @var float
         */
        'margin_bottom' => 25,

        /**
         * Whether to use Unicode
         *
         * @var bool
         */
        'is_unicode' => true,
    ],

    'options' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],
];
