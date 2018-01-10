<?php namespace Apsis;

?>
<div class="wp-author-info">
    <?= wp_nonce_field(SECURITY_NONCE_VALUE, SECURITY_NONCE_KEY); ?>

    <table class="form-table">
        <tbody>
            <tr>
                <th>
                    <label for="first_name"><?= __('First Name', 'apsis_wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="first_name" name="<?= AUTHOR_FIRST_NAME_META_ID; ?>" class="regular-text" value="<?= get_post_meta($post->ID, AUTHOR_FIRST_NAME_META_ID, true); ?>">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="last_name"><?= __('Last Name', 'apsis_wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="last_name" name="<?= AUTHOR_LAST_NAME_META_ID; ?>" class="regular-text" value="<?= get_post_meta($post->ID, AUTHOR_LAST_NAME_META_ID, true); ?>">
                </td>
            </tr>

            <tr>
                <th>
                    <label><?= __('Avatar', 'apsis_wp'); ?></label>
                </th>
                <td>
                    <?php $avatar_id = get_post_meta($post->ID, AUTHOR_AVATAR_META_ID, true); ?>
                    <?php $avatar_src = wp_get_attachment_image_src($avatar_id, 'medium'); ?>

                    <div id="avatar_preview">
                        <?php if ($avatar_id && $avatar_src) : ?>
                            <img src="<?= $avatar_src[0]; ?>">
                        <?php endif; ?>
                    </div>

                    <input type="url" name="avatar" id="avatar" value="<?= $avatar_src[0]; ?>" readonly>
                    <input type="hidden" name="<?= AUTHOR_AVATAR_META_ID; ?>" id="avatar_id" value="<?= get_post_meta($post->ID, AUTHOR_AVATAR_META_ID, true); ?>">

                    <button class="button upload_image">
                        <?= __('Set Avatar', 'apsis_wp'); ?>
                    </button>
                    <a href="#" id="remove_avatar" class="<?= $avatar_id ? '' : 'hidden'; ?>">
                        <?= __('Remove Image', 'apsis_wp'); ?>
                    </a>
                </td>
            </tr>

            <tr>
                <th>
                    <label><?= __('Bio', 'apsis_wp'); ?></label>
                </th>
                <td>
                    <?= wp_editor(html_entity_decode(get_post_meta($post->ID, AUTHOR_BIO_META_ID, true)), AUTHOR_BIO_META_ID) ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
