
<div data-editor-config="<?php echo $model['editor_config'] ?>" class="<?php echo esc_attr(join(' ', $model['classes'])) ?>">
	<div class="dz-image" style="display:flex;justify-content:center;flex-flow:column;width:80px;padding-right:12px">
		<img style="max-width:100%" src="" alt="" data-dz-thumbnail />
	</div>
	<div class="dz-filename" data-dz-name></div>
	<div class="dz-left">
		<div class="dz-progress-wrapper">
			<div class="dz-progress"></div>
			<div class="dz-upload" data-dz-uploadprogress></div>
		</div>
		<div class="dz-options">
			<?php if( $model['editor_enabled'] ) { ?>
			<div class="start-crop wapf-tt-wrap" data-dir="t"  data-tip="<?php _e('Edit image', 'wapf-aiu') ?>" style="display:none">
                <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width=".8rem" height=".8rem" viewBox="0 0 512 512"><path d="M497.9 142.1l-46.1 46.1c-4.7 4.7-12.3 4.7-17 0l-111-111c-4.7-4.7-4.7-12.3 0-17l46.1-46.1c18.7-18.7 49.1-18.7 67.9 0l60.1 60.1c18.8 18.7 18.8 49.1 0 67.9zM284.2 99.8L21.6 362.4.4 483.9c-2.9 16.4 11.4 30.6 27.8 27.8l121.5-21.3 262.6-262.6c4.7-4.7 4.7-12.3 0-17l-111-111c-4.8-4.7-12.4-4.7-17.1 0zM88 424h48v36.3l-64.5 11.3-31.1-31.1L51.7 376H88v48z"/></svg>
			</div>
			<?php } ?>
            <div class="dz-remove" data-dz-remove>
                <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1rem" height="1rem" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
            </div>
		</div>
	</div>
</div>




<div class="dz-preview">
    <div class="dz-filename" data-dz-name></div>
    <div class="dz-left">
        <div class="dz-progress-wrapper">
            <div class="dz-progress"></div>
            <div class="dz-upload" data-dz-uploadprogress></div>
        </div>
        <div>
            <span><a href="#" class="lcp-open-editor">' . __( 'Customize', 'wapf-lcp' ) . '</a></span>
        </div>
        <div>
            <span class="lcp-dz-loader"></span>
        </div>
        <div class="dz-remove" data-dz-remove> 
            <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="width: 1rem;height:1rem" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
        </div>
    </div>
</div>





<div class="dz-preview">
    <div class="dz-image" style="display:flex;justify-content:center;flex-flow:column;width:80px;padding-right:12px">
        <img style="max-width:100%" src="" alt="" data-dz-thumbnail />
    </div>
    <div class="dz-filename" data-dz-name></div>
    <div class="dz-left">
        <div class="dz-progress-wrapper">
            <div class="dz-progress"></div>
            <div class="dz-upload" data-dz-uploadprogress></div>
        </div>
        apply_filters( 'wapf/html/file_entry', '' )
        <div class="dz-remove" data-dz-remove> 
            <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>
        </div>
    </div>
</div>




<div class="dz-preview"><div class="dz-image" style="display:flex;justify-content:center;flex-flow:column;width:80px;padding-right:12px"><img style="max-width:100%" src="" alt="" data-dz-thumbnail /></div><div class="dz-filename" data-dz-name></div><div class="dz-left"><div class="dz-progress-wrapper"><div class="dz-progress"></div><div class="dz-upload" data-dz-uploadprogress></div></div><div class="dz-remove" data-dz-remove><svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg></div></div></div>