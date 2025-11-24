<?php /** @var array $model */ ?>

<div class="wapf-toggle" rv-unique-checkbox>
    <input rv-on-change="onChange" rv-checked="field.img_editor" type="checkbox" >
    <label class="wapf-toggle-label" for="wapf-toggle-">
        <span class="wapf-toggle-inner" data-true="Yes" data-false="No"></span>
        <span class="wapf-toggle-switch"></span>
    </label>
</div>

<div style="padding:15px;position: relative;margin-top:15px;border-radius: 6px;background: #f5f5f5;" rv-show="field.img_editor">
    <div style="position: absolute;top:-5px;left: 15px;width: 15px;height: 15px;background: #f5f5f5;transform: rotate(45deg);"></div>
    <div style="display:flex;justify-content: space-between;align-items: center;">
        <div>
            <strong><?php _e('Enable image cropper','wapf-aiu'); ?></strong>
        </div>
        <div>
            <div class="wapf-toggle wapf-toggle--small" rv-unique-checkbox>
                <input rv-on-change="onChange" rv-checked="field.img_cropper" type="checkbox" >
                <label class="wapf-toggle-label" for="wapf-toggle-">
                    <span class="wapf-toggle-inner" data-true="" data-false=""></span>
                    <span class="wapf-toggle-switch"></span>
                </label>
            </div>
        </div>
    </div>

    <div rv-show="field.img_cropper">
        <div style="padding-top:15px;display:flex;justify-content: space-between;align-items: center;">
            <div>
                <strong><?php _e('Aspect ratio','wapf-aiu'); ?></strong>
            </div>
            <div>
                <select style="max-width: 180px;text-align: right" rv-default="field.cropper_ratio" data-default="" rv-on-change="onChange" rv-value="field.cropper_ratio">
                    <?php
                    foreach( $model['ratios'] as $id => $ratio ) {
                        echo '<option value="' . $id . '">' . $ratio . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div style="padding-top:15px;display:flex;justify-content: space-between;align-items: center;">
        <div>
            <strong><?php _e('Enable image rotation','wapf-aiu'); ?></strong>
        </div>
        <div>
            <div class="wapf-toggle wapf-toggle--small" rv-unique-checkbox>
                <input rv-on-change="onChange" rv-checked="field.img_rotate" type="checkbox" >
                <label class="wapf-toggle-label" for="wapf-toggle-">
                    <span class="wapf-toggle-inner" data-true="" data-false=""></span>
                    <span class="wapf-toggle-switch"></span>
                </label>
            </div>
        </div>
    </div>

    <div style="padding-top:15px;display:flex;justify-content: space-between;align-items: center;">
        <div>
            <strong><?php _e('Enable image flipping','wapf-aiu'); ?></strong>
        </div>
        <div>
            <div class="wapf-toggle wapf-toggle--small" rv-unique-checkbox>
                <input rv-on-change="onChange" rv-checked="field.img_flip" type="checkbox" >
                <label class="wapf-toggle-label" for="wapf-toggle-">
                    <span class="wapf-toggle-inner" data-true="" data-false=""></span>
                    <span class="wapf-toggle-switch"></span>
                </label>
            </div>
        </div>
    </div>

    <div style="padding-top:15px;display:flex;justify-content: space-between;align-items: center;">
        <div>
            <strong><?php _e('How to open the image editor?','wapf-aiu'); ?></strong>
            <div><?php _e('When and how should the editor be shown?','wapf-aiu'); ?></div>
        </div>
        <div>
            <select style="max-width: 180px;text-align: right" rv-default="field.editor_open" data-default="" rv-on-change="onChange" rv-value="field.editor_open">
                <option value=""><?php _e('An "edit" button','wapf-aiu') ?></option>
                <option value="auto"><?php _e('"edit" button + automatically open','wapf-aiu') ?></option>
                <option value="crop_only"><?php _e('Only open if cropping is required','wapf-aiu') ?></option>
            </select>
        </div>
    </div>

</div>