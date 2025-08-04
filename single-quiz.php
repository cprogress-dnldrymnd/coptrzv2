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
            <h3>
                <?= $question_text ?>
            </h3>
        </div>
    </section>
<?php } ?>
<?php get_footer('clean') ?>