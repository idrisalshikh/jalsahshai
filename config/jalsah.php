<?php
return [
    'session' => [
        'code' => 'JALSAH123',
        'video_url' => '/video.mp4',
        'status' => 'waiting',
        'questions' => [
            [
                'type' => 'mcq',
                'text' => 'ما هي عاصمة ماليزيا؟',
                'options' => ['كوالالمبور', 'جاكرتا', 'سنغافورة'],
                'correct_index' => 0,
            ],
            [
                'type' => 'true_false',
                'text' => 'الشاي الأخضر مفيد للصحة.',
                'correct' => true,
            ],
        ],
    ],
];
