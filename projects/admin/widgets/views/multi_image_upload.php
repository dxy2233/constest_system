<div style="position: relative; display: block; border: 1px solid gray; display: none;float:left;margin:0 10px 10px 0;" id="attachement_template_<?php echo $this->context->id; ?>" class="form-inline upload_img_result">
    <img src="/static/image/close.png" onclick="deleteAttachement(this);" style="position: absolute;right:0; top:0;width:20px;"/>
    <a href="" target="_blank" id="file_link" style="display: block;"><img src="" style="width: <?=$this->context->showImgWidth?>px;height:<?=$this->context->showImgHeight?>px;" /></a>
    <input style="padding: 5px;width: 100%;box-sizing: border-box;" type="text" name="attachmentNames[]" class="form-control" />
    <input type="hidden" name="attachmentUrls[]" />
</div>
<div style="line-height:50px;width:450px; display: none;clear:both;" id="attachement_template_others_<?php echo $this->context->id; ?>" class="form-inline upload_img_result">
    <label style="font-weight:normal;">名称:</label>
    <span class="space-5"></span>
    <input type="text" name="attachmentNames[]" class="form-control" />
    <span class="space-5"></span>
    <a href="" target="_blank" id="file_link"></a>
    <span class="space-5"></span>
    <input type="hidden" name="attachmentUrls[]"  />
    <a href="#" onclick="deleteAttachement(this);return false;">删除</a>
</div>

<div class="form-group">
    <label
        class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?> uploadImgs uploadimgsxiugai">
        <div style="clear:both;text-align: left;">
            <iframe
                src="/uploader/default/<?php echo $this->context->useOss ? 'oss-' : ''; ?>image-template?context=<?php echo $this->context->context; ?>&callback=uploadProject_<?php echo $this->context->id; ?>&localImage=false&maxFiles=<?php echo $this->context->maxFiles; ?>"
                width="400px" height="50px" style="border: 0px;" scrolling="no" frameborder="0"></iframe>
            <input type="input" style="width:0px;height:0px;position: relative;left: -1000px;" name="attachments"
                   data-val="true" data-val-attachments="必须上传相关附件">
            <span class="field-validation-valid" data-valmsg-for="attachments" data-valmsg-replace="true"></span>
        </div>
        <div style="clear:both;overflow: hidden;" id="attachment_container_<?php echo $this->context->id; ?>">
        </div>
    </div>
</div>

<script>
    function uploadProject_<?php echo $this->context->id; ?>(data) {
        var container = $('#attachment_container_<?php echo $this->context->id; ?>');
        if (container.children().filter('div').length >= <?php echo $this->context->maxFiles; ?>) {
            $.dz.DG.errorMessage('最多只能上传<?php echo $this->context->maxFiles; ?>张图片');
            return false;
        }

        var index1 = data.url.lastIndexOf(".");
        var index2 = data.url.length;
        var ext = data.url.substring(index1, index2);
        var type = ext;
        if (type == '.xlsx' || type == '.xls' || type == '.pdf') {
            var attachementItem = $('#attachement_template_others_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('input').eq(0).attr('name', "<?php echo $this->context->name; ?>_names[]").val(data.originalName.split('.')[0]);
            attachementItem.find('#file_link').attr('href', data.url);
            attachementItem.find('#file_link').text(data.originalName);
            attachementItem.find('input').eq(1).attr('name', "<?php echo $this->context->name; ?>_urls[]").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
        } else {

            <?php  if($this->context->diyInputPlaceholder): ?>
            var attachementItem = $('#attachement_template_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('input').eq(0).attr('name', '<?php echo $this->context->name; ?>_names[]').val(data.originalName.split('.')[0]);
            attachementItem.find('img').eq(1).attr('src', data.url);
            attachementItem.find('a').attr('href', data.url);
            attachementItem.find('input').eq(1).attr('name', "<?php echo $this->context->name; ?>_urls[]").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
            <?php else:?>
            var attachementItem = $('#attachement_template_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('input').eq(0).attr('name', '<?php echo $this->context->name; ?>_names[]').val(data.originalName.split('.')[0]);
            attachementItem.find('img').eq(1).attr('src', data.url);
            attachementItem.find('a').attr('href', data.url);
            attachementItem.find('input').eq(1).attr('name', "<?php echo $this->context->name; ?>_urls[]").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
            <?php endif;?>


        }

//        $.dz.DG.ajustPosition();

    }
    function deleteAttachement(self) {
        $(self).closest('div').remove();
    }

    <?php if ($this->context->attachments){ ?>
    (function loadAttachments_<?php echo $this->context->id; ?>() {
        var attachments = <?php echo json_encode($this->context->attachments); ?>;

        for (var index in attachments) {
            var data = {
                'originalName': attachments[index]['name'],
                'url': attachments[index]['url'],
            };
            uploadProject_<?php echo $this->context->id; ?>(data);
        }
    })();
    <?php } ?>

</script>
