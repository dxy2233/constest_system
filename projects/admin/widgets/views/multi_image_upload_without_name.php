<div style="float:left;display: none;border: 1px dashed gray; padding: 2px;margin-right:10px;margin-bottom: 10px;" id="attachement_template_<?php echo $this->context->id; ?>" class="form-inline upload_img_result">
    <a href="" target="_blank"><img src="" style="width: 100px;height:100px;" /></a>
    <input type="hidden" name="<?php echo $this->context->name; ?>" />
    <p style="text-align: center;line-height: 18px;">
    <a href="#" style="color:red;" onclick="deleteAttachement(this);return false;">删除</a>
    </p>
</div>

<div class="form-group">
    <label
        class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <div>
            <iframe
                src="/uploader/default/<?php echo $this->context->useOss ? 'oss-' : ''; ?>image-template?context=<?php echo $this->context->context; ?>&callback=uploadProject_<?php echo $this->context->id; ?>&localImage=false"
                width="150px" height="70px" style="border: 0px;" scrolling="no" frameborder="0"></iframe>
            <input type="input" style="width:0px;height:0px;position: relative;left: -1000px;" name="attachments"
                   data-val="true" data-val-attachments="必须上传相关附件">
            <span class="field-validation-valid" data-valmsg-for="attachments" data-valmsg-replace="true"></span>
        </div>
        <div style="overflow: hidden" id="attachment_container_<?php echo $this->context->id; ?>">
        </div>
    </div>
</div>

<script>

    function uploadProject_<?php echo $this->context->id; ?>(data) {
        var renderUI = <?php echo $this->context->renderUI ? 'true' : 'false'; ?>;

        //需要渲染，提供默认的实现
        if (renderUI) {
            var container = $('#attachment_container_<?php echo $this->context->id; ?>');
            if (container.children().filter('div').length >= <?php echo $this->context->maxFiles; ?>) {
                $.dz.DG.errorMessage('最多只能上传<?php echo $this->context->maxFiles; ?>张图片');
                return false;
            }

            var attachementItem = $('#attachement_template_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('img').attr('src', data.url);
            attachementItem.find('a').attr('href', data.url);
            attachementItem.find('input').eq(0).attr('name', "<?php echo $this->context->name; ?>").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
        } else {
            var renderCallback = '<?php echo $this->context->renderCallback; ?>';
            eval(renderCallback + '(data.url);');
        }
    }
    function deleteAttachement(self) {
        $(self).closest('div').remove();
    }

    <?php if ($this->context->attachments){ ?>
    (function loadAttachments_<?php echo $this->context->id; ?>() {
        var attachments = <?php echo json_encode($this->context->attachments); ?>;
        for (var index in attachments) {
            var data = {
                'url': attachments[index],
            };
            uploadProject_<?php echo $this->context->id; ?>(data);
        }
    })();
    <?php } ?>

</script>
