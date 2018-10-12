<?php use admin\widgets\SingleFileUploadWidget; ?>

<script id="container_<?php echo $this->context->id;?>" name="<?php echo $this->context->name; ?>" type="text/plain"><?php echo $this->context->content; ?></script>
<!-- 配置文件 -->
<script type="text/javascript" src="/static/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="/static/ueditor/ueditor.all.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue_<?php echo $this->context->id;?> = UE.getEditor('container_<?php echo $this->context->id;?>');

    UE.registerUI('button', function(editor, uiName) {
        //注册按钮执行时的command命令，使用命令默认就会带有回退操作
        editor.registerCommand(uiName, {
            execCommand: function() {
                $('#upload_modal_<?php echo $this->context->id; ?>').modal();
            }
        });
        //创建一个button
        var btn = new UE.ui.Button({
            //按钮的名字
            name: uiName,
            //提示
            title: uiName,
            //添加额外样式，指定icon图标，这里默认使用一个重复的icon
            cssRules: 'background-position: -500px 0;',
            //点击时执行的命令
            onclick: function() {
                //这里可以不用执行命令,做你自己的操作也可
                editor.execCommand(uiName);
            }
        });

        return btn;
    });
</script>




<!-- Modal -->
<div class="modal fade" id="upload_modal_<?php echo $this->context->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">上传图片</h4>
        </div>
        <div class="modal-body" style="width:1100px;overflow: hidden;">
            <?php echo SingleFileUploadWidget::widget([
                    'name' => 'upload_' . $this->context->id,
                    'context' => 'cms'
            ]); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="insert_pic_<?php echo $this->context->id; ?>();">确定</button>
        </div>
    </div>
</div>
</div>
<script>
    function insert_pic_<?php echo $this->context->id; ?>() {
        ue_<?php echo $this->context->id;?>.execCommand('inserthtml', '<img src="' +
            $('#upload_modal_<?php echo $this->context->id; ?> img').attr('src') + '" />');
        $('#upload_modal_<?php echo $this->context->id; ?>').modal('hide');
    }
</script>