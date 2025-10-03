<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $gamesData = [
                [
                    'name' => 'جلسة التوعية الأولى: نظافة الأيدي',
                    'description' => 'فيديو قصير وأسئلة تفاعلية عن أهمية غسل اليدين.',
                    'video_url' => 'video_placeholder_1.mp4',
                    'questions' => [
                        [
                            'text' => 'ما هي أفضل طريقة لغسل اليدين؟',
                            'options' => ["بالماء فقط", "بالماء والصابون لمدة ٢٠ ثانية", "بالمحارم المبللة", "سبع مرات بالماء ومرة بالتراب"],
                            'correct_answer' => 1,
                        ],
                        [
                            'text' => 'كم مرة يجب أن نغسل أيدينا في اليوم على الأقل؟',
                            'options' => ["مرة واحدة", "ثلاث مرات", "خمس مرات أو أكثر عند الحاجة", "4", "5"],
                            'correct_answer' => 2,
                        ],
                        [
                            'text' => 'لماذا ناكل ونشرب',
                            'options' => ["لنشبع", "لنستعين بالطاقة لعبادة الله", "نستمتع بنعم الله علينا", "4", "5", "6"],
                            'correct_answer' => 1,
                        ],
                        [
                            'text' => 'غسل اليدين بالماء والصابون يقتل الجراثيم.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0, // true = 0 for True
                        ],
                        [
                            'text' => 'هل يجوز التطهر بالماء الطاهر',
                            'options' => ["True", "False"],
                            'correct_answer' => 1, // false = 1 for False
                        ],
                        [
                            'text' => 'الماء الطاهر مثل الشاهي',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الثانية: أهمية الوضوء',
                    'description' => 'فيديو قصير وأسئلة تفاعلية حول الوضوء وفوائده.',
                    'video_url' => 'video_placeholder_2.mp4',
                    'questions' => [
                        [
                            'text' => 'ما هو أول فرض من فروض الوضوء؟',
                            'options' => ["غسل الوجه", "المضمضة", "النية واستحضار القلب"],
                            'correct_answer' => 2,
                        ],
                        [
                            'text' => 'الوضوء شرط لصحة الصلاة.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'أي من التالية لا ينقض الوضوء؟',
                            'options' => ["النوم العميق", "الأكل والشرب", "الريح"],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الثالثة: آداب المسجد',
                    'description' => 'تعرف على آداب دخول المسجد والصلاة فيه.',
                    'video_url' => 'video_placeholder_3.mp4',
                    'questions' => [
                        [
                            'text' => 'أي دعاء يقال عند دخول المسجد؟',
                            'options' => ["دعاء السفر", "اللهم افتح لي أبواب رحمتك", "دعاء الخروج من المنزل"],
                            'correct_answer' => 1,
                        ],
                        [
                            'text' => 'يجوز البيع والشراء داخل المسجد.',
                            'options' => ["True", "False"],
                            'correct_answer' => 1,
                        ],
                        [
                            'text' => 'ماذا تفعل إذا دخلت المسجد والإمام راكع في صلاة الجماعة؟',
                            'options' => ["تنتظر حتى يقوم الإمام", "تكبر تكبيرة الإحرام ثم تركع مباشرة", "تبدأ صلاة جديدة بمفردك"],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الرابعة: فضل الصدقة',
                    'description' => 'فيديو ملهم عن أهمية الصدقة وأثرها.',
                    'video_url' => 'video_placeholder_4.mp4',
                    'questions' => [
                        [
                            'text' => 'الصدقة تطفئ غضب الرب.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'أي من هذه الأعمال يعتبر صدقة؟',
                            'options' => ["الابتسامة في وجه أخيك", "إماطة الأذى عن الطريق", "الكلمة الطيبة", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                        [
                            'text' => 'تقتصر الصدقة على إعطاء المال للفقراء فقط.',
                            'options' => ["True", "False"],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الخامسة: أهمية صلاة الجماعة',
                    'description' => 'فضل صلاة الجماعة وأحكامها.',
                    'video_url' => 'video_placeholder_5.mp4',
                    'questions' => [
                        [
                            'text' => 'كم تزيد صلاة الجماعة عن صلاة الفرد؟',
                            'options' => ["سبع درجات", "سبع وعشرون درجة", "عشر درجات"],
                            'correct_answer' => 1,
                        ],
                        [
                            'text' => 'صلاة الجماعة واجبة على الرجال في المسجد.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة السادسة: بر الوالدين',
                    'description' => 'مكانة بر الوالدين في الإسلام.',
                    'video_url' => 'video_placeholder_6.mp4',
                    'questions' => [
                        [
                            'text' => 'رضا الله في رضا الوالدين.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'ما هو أقل مراتب العقوق؟',
                            'options' => ["التأفف", "الصراخ", "عدم الطاعة"],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة السابعة: صلة الرحم',
                    'description' => 'أهمية صلة الأقارب وفوائدها.',
                    'video_url' => 'video_placeholder_7.mp4',
                    'questions' => [
                        [
                            'text' => 'صلة الرحم تزيد في العمر والرزق.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'من هم الأرحام الواجب صلتهم؟',
                            'options' => ["الأقارب من جهة الأب فقط", "الأقارب من جهة الأم فقط", "كل قريب يجمعك به رحم"],
                            'correct_answer' => 2,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الثامنة: حفظ اللسان',
                    'description' => 'خطورة اللسان وأهمية حفظه عن المحرمات.',
                    'video_url' => 'video_placeholder_8.mp4',
                    'questions' => [
                        [
                            'text' => 'أكثر خطايا ابن آدم من لسانه.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'أي مما يلي من آفات اللسان؟',
                            'options' => ["الغيبة", "النميمة", "الكذب", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة التاسعة: فضل ذكر الله',
                    'description' => 'أهمية الذكر وأنواعه.',
                    'video_url' => 'video_placeholder_9.mp4',
                    'questions' => [
                        [
                            'text' => 'أفضل الذكر لا إله إلا الله.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'متى يستحب الإكثار من ذكر الله؟',
                            'options' => ["بعد الصلوات", "في الصباح والمساء", "في كل وقت وحين"],
                            'correct_answer' => 2,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة العاشرة: آداب الطعام',
                    'description' => 'سنن وآداب تناول الطعام.',
                    'video_url' => 'video_placeholder_10.mp4',
                    'questions' => [
                        [
                            'text' => 'من السنة التسمية قبل الأكل.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'بأي يد يستحب الأكل والشرب؟',
                            'options' => ["باليد اليمنى", "باليد اليسرى", "بكلتا اليدين"],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الحادية عشر: المحافظة على البيئة',
                    'description' => 'حث الإسلام على النظافة والعناية بالبيئة.',
                    'video_url' => 'video_placeholder_11.mp4',
                    'questions' => [
                        [
                            'text' => 'إماطة الأذى عن الطريق شعبة من الإيمان.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'ما هو حكم تلويث الماء النقي؟',
                            'options' => ["مباح", "مكروه", "محرم إذا كان يضر"],
                            'correct_answer' => 2,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الثانية عشر: حقوق الجار',
                    'description' => 'أهمية الإحسان إلى الجار.',
                    'video_url' => 'video_placeholder_12.mp4',
                    'questions' => [
                        [
                            'text' => 'لا يدخل الجنة من لا يأمن جاره بوائقه.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'من صور الإحسان إلى الجار؟',
                            'options' => ["عيادته إذا مرض", "تعزيته إذا أصيب بمصيبة", "كف الأذى عنه", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الثالثة عشر: التوكل على الله',
                    'description' => 'معنى التوكل وثمراته.',
                    'video_url' => 'video_placeholder_13.mp4',
                    'questions' => [
                        [
                            'text' => 'التوكل يعني ترك الأسباب.',
                            'options' => ["True", "False"],
                            'correct_answer' => 1,
                        ],
                        [
                            'text' => 'ما هي ثمرة التوكل على الله؟',
                            'options' => ["الرزق", "الكفاية", "الطمأنينة", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الرابعة عشر: أهمية العلم',
                    'description' => 'فضل طلب العلم في الإسلام.',
                    'video_url' => 'video_placeholder_14.mp4',
                    'questions' => [
                        [
                            'text' => 'طلب العلم فريضة على كل مسلم.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'ما هو أول ما نزل من القرآن الكريم؟',
                            'options' => ["سورة الفاتحة", "آيات من سورة العلق تأمر بالقراءة", "سورة الإخلاص"],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الخامسة عشر: الصبر عند البلاء',
                    'description' => 'فضل الصبر وأهميته عند المصائب.',
                    'video_url' => 'video_placeholder_15.mp4',
                    'questions' => [
                        [
                            'text' => 'إنما يوفى الصابرون أجرهم بغير حساب.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'ماذا يقول المسلم عند وقوع المصيبة؟',
                            'options' => ["يتسخط ويجزع", "يصبر ويحتسب ويقول إنا لله وإنا إليه راجعون", "يلوم القدر"],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة السادسة عشر: الأمانة',
                    'description' => 'حث الإسلام على أداء الأمانات.',
                    'video_url' => 'video_placeholder_16.mp4',
                    'questions' => [
                        [
                            'text' => 'من صفات المنافق إذا اؤتمن خان.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'تشمل الأمانة:',
                            'options' => ["أمانة المال", "أمانة العمل", "أمانة الكلام", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة السابعة عشر: الشكر',
                    'description' => 'فضل الشكر وكيف يكون.',
                    'video_url' => 'video_placeholder_17.mp4',
                    'questions' => [
                        [
                            'text' => 'لئن شكرتم لأزيدنكم.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'يكون الشكر بـ:',
                            'options' => ["اللسان", "القلب", "الجوارح", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة الثامنة عشر: آداب النوم',
                    'description' => 'سنن وآداب النوم في الإسلام.',
                    'video_url' => 'video_placeholder_18.mp4',
                    'questions' => [
                        [
                            'text' => 'من السنة الوضوء قبل النوم.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'على أي شق يستحب النوم؟',
                            'options' => ["الأيمن", "الأيسر", "على البطن"],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة التاسعة عشر: احترام الكبير',
                    'description' => 'أهمية توقير الكبير ورحمة الصغير.',
                    'video_url' => 'video_placeholder_19.mp4',
                    'questions' => [
                        [
                            'text' => 'ليس منا من لم يرحم صغيرنا ويوقر كبيرنا.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'من صور احترام الكبير:',
                            'options' => ["تقديمه في الكلام والمشي", "الاستماع إليه بإنصات", "عدم مقاطعته", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
                [
                    'name' => 'الجلسة العشرون: الحياء',
                    'description' => 'فضل الحياء وأنه شعبة من الإيمان.',
                    'video_url' => 'video_placeholder_20.mp4',
                    'questions' => [
                        [
                            'text' => 'الحياء لا يأتي إلا بخير.',
                            'options' => ["True", "False"],
                            'correct_answer' => 0,
                        ],
                        [
                            'text' => 'الحياء من:',
                            'options' => ["الله", "الناس", "النفس", "كل ما سبق"],
                            'correct_answer' => 3,
                        ],
                    ],
                ],
            ];

            foreach ($gamesData as $gameData) {
                $questionsData = $gameData['questions'];
                unset($gameData['questions']);

                $game = Game::create($gameData);

                foreach ($questionsData as $questionData) {
                    $questionData['game_id'] = $game->id;
                    $question = Question::create($questionData);
                    $game->questions()->attach($question->id);
                }
            }
        });
    }
}
