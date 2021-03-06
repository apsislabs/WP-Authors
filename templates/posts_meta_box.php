<?php namespace Apsis;

?>

<div class="wp-authors">
    <?= wp_nonce_field(SECURITY_NONCE_VALUE, SECURITY_NONCE_KEY); ?>

    <div class="field">
        <label for="ap_authors"><?= __('Select Author', 'apsis_wp'); ?></label>
        <select name="<?= AUTHOR_META_ID; ?>" id="ap_authors">
            <option value=""></option>

            <?php foreach ($authors as $author) : ?>
                <option value="<?= $author->ID; ?>" <?= selected(Utils::getPostAuthor($post->ID), $author->ID); ?>>
                    <?= $author->post_title; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <p>
        <a href="#" class="hide-if-no-js" id="add_author_toggle">
            + <?= __('Add Author', 'apsis_wp'); ?>
        </a>
    </p>

    <div id="new_author">
        <?= wp_nonce_field(AJAX_NONCE_VALUE, AJAX_NONCE_KEY); ?>

        <div class="clear-on-success">
            <div class="field">
                <label for="first_name"><?= __('First Name', 'apsis_wp'); ?></label>
                <input type="text" class="large-text" id="first_name" name="first_name">
            </div>

            <div class="field">
                <label for="last_name"><?= __('Last Name', 'apsis_wp'); ?></label>
                <input type="text" class="large-text" id="last_name" name="last_name">
            </div>
        </div>

        <div class="field">
            <button id="add_author" class="button">
                <?= __('Add Author', 'apsis_wp'); ?>
            </button>
        </div>
    </div>
</div>
