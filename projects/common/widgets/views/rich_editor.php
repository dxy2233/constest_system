<script src="/static/tinymce/tinymce.min.js"></script>
<script src="/static/js/jquery.form.min.js"></script>
<script src="/static/tinymce/plugins/imageupload/plugin.min.js"></script>
<style>
    .mce-container-body  label{
        display:none;
    }
    .mce-container-body input[type="file"] {
        border: 0;
    }
</style>
<div class="form-group form-group-visible">
    <label class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <textarea id="editor_<?php echo $this->context->id; ?>" class="" name="<?php echo $this->context->name; ?>" style="width: 100%;"><?php echo $this->context->content; ?></textarea>
        <script>
            $(function()
            {
                tinymce.init({
                    selector: "#editor_<?php echo $this->context->id; ?>",
                    language: 'zh_CN',
                    height: 300,
                    imageupload_url: '/upload/tinymce-image',
                    plugins: [
                        "advlist autolink autosave link  image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern imageupload"
                    ],

                    toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | forecolor backcolor styleselect formatselect fontselect fontsizeselect",
                    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor imageupload image media code | insertdatetime preview",
                    toolbar3: "table | hr removeformat | subscript superscript | charmap | fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking pagebreak restoredraft",

                    menubar: false,
                    toolbar_items_size: 'small',

                    style_formats: [{
                        title: 'Bold text',
                        inline: 'b'
                    }, {
                        title: 'Red text',
                        inline: 'span',
                        styles: {
                            color: '#ff0000'
                        }
                    }, {
                        title: 'Red header',
                        block: 'h1',
                        styles: {
                            color: '#ff0000'
                        }
                    }, {
                        title: 'Example 1',
                        inline: 'span',
                        classes: 'example1'
                    }, {
                        title: 'Example 2',
                        inline: 'span',
                        classes: 'example2'
                    }, {
                        title: 'Table styles'
                    }, {
                        title: 'Table row 1',
                        selector: 'tr',
                        classes: 'tablerow1'
                    }],

                    templates: [{
                        title: 'Test template 1',
                        content: 'Test 1'
                    }, {
                        title: 'Test template 2',
                        content: 'Test 2'
                    }],
                });
            });
        </script>
    </div>
</div>