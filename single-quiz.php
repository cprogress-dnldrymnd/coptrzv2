<?php get_header('clean'); ?>
<?php the_content(); ?>
<?php
$quiz_questions = get__post_meta('quiz_questions');
?>

<?php foreach ($quiz_questions as $question) { ?>
    <?php
    $question_key = $question['question_key'];
    $question_type = $question['question_type'];
    $question_text = $question['question_text'];
    $question_choices = $question['question_choices'];
    $conditional_logic = $question['conditional_logic'];
    ?>
    <section class="quiz-question--section">
        <div class="container">
            <h3 class="question--text">
                <?= $question_text ?>
            </h3>
            <div class="question--choices">
                <div class="row g-4 justify-content-center">
                    <?php foreach ($question_choices as $question_choice) { ?>
                        <div class="col-lg-4">
                            <input type="<?= $question_type ?>" name="<?= $question_key ?>" value="<?= $question_choice['choice'] ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php get_footer('clean') ?>