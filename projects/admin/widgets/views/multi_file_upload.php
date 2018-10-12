<div style="line-height:50px;width:450px; display: none;clear:both;" id="attachement_template_<?php echo $this->context->id; ?>" class="form-inline upload_img_result">
    <label style="font-weight:normal;">名称:</label>
    <span class="space-5"></span>
    <input type="text" name="attachmentNames[]" class="form-control" />
    <span class="space-5"></span>
    <a href="" target="_blank" id="file_link"><img src="" style="width: 50px;height:50px;" /></a>
    <span class="space-5"></span>
    <input type="hidden" name="attachmentUrls[]" />
    <a href="#" onclick="deleteAttachement(this);return false;">删除</a>
</div>
<div style="line-height:50px;width:450px; display: none;clear:both;" id="attachement_template_others_<?php echo $this->context->id; ?>" class="form-inline upload_img_result">
    <label style="font-weight:normal;">名称:</label>
    <span class="space-5"></span>
    <input type="text" name="attachmentNames[]" class="form-control" />
    <span class="space-5"></span>
    <a href="" target="_blank" id="file_link"></a>
    <span class="space-5"></span>
    <input type="hidden" name="attachmentUrls[]" />
    <a href="#" onclick="deleteAttachement(this);return false;">删除</a>
</div>

<div>
    <iframe frameborder="0" src="<?php echo $frameUrl; ?>" height="30px" style="border: 0px;" scrolling="no"></iframe>
</div>
<div id="attachment_container_<?php echo $this->context->id; ?>">
</div>
<script>
    function uploadProject_<?php echo $this->context->id; ?>(data) {
        var index1 = data.url.lastIndexOf(".");
        var index2 = data.url.length;
        var ext = data.url.substring(index1, index2);
        var type = ext;
        if (type == '.xlsx' || type == '.xls' || type == '.pdf') {
            var attachementItem = $('#attachement_template_others_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('input').eq(0).attr('name', "<?php echo $this->context->name; ?>_names[]").val(data.name.split('.')[0]);
            attachementItem.find('#file_link').attr('href', data.url);
            attachementItem.find('#file_link').text(data.originalName);
            attachementItem.find('input').eq(1).attr('name', "<?php echo $this->context->name; ?>_urls[]").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
        } else {
            var attachementItem = $('#attachement_template_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('input').eq(0).attr('name', '<?php echo $this->context->name; ?>_names[]').val(data.name.split('.')[0]);
            attachementItem.find('img').attr('src', data.url);
            attachementItem.find('a').attr('href', data.url);
            attachementItem.find('input').eq(1).attr('name', "<?php echo $this->context->name; ?>_urls[]").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
        }

    }
    function deleteAttachement(self) {
        $(self).parent().remove();
    }

    <?php if ($this->context->attachments){ ?>
    (function loadAttachments_<?php echo $this->context->id; ?>() {
        var attachments = <?php echo json_encode($this->context->attachments); ?>;
        console.log(attachments);
        for (var index in attachments) {
            var data = {
                'name': attachments[index]['name'],
                'url': attachments[index]['url'],
            };
            uploadProject_<?php echo $this->context->id; ?>(data);
        }
    })();
    <?php } ?>

</script>
