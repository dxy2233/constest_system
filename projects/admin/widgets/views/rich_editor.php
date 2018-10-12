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
                    height: 500,
                    plugins: [
                        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
                    ],

                    toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify upload | forecolor backcolor styleselect formatselect fontselect fontsizeselect",
                    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview",
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

                    setup: function (editor) {
                        editor.addButton('upload', {
                            text: '上传图片',
                            icon: false,
                            onclick: function () {
                                $.dz.DG.loadUrl('/richupload/default/index', '上传图片');
                            }
                        });
                    },
                });
            });
        </script>
    </div>
    <div class="form-group-clear"></div>
</div>


<script>
    function insertPicToRichEditor(url) {
        tinymce.get('editor_<?php echo $this->context->id; ?>').insertContent('<img src="' + url + '"/>');
        $('#upload_modal_<?php echo $this->context->id; ?>').modal('hide');
        $.dz.DG.close();
    }
</script>