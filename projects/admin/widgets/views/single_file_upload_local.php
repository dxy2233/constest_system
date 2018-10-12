
<iframe frameborder="0" src="/uploader/default/image-template?context=<?php echo $this->context->context;?>&callback=uploadProject_<?php echo $this->context->id;?>&localImage=true&fileName=<?php echo $this->context->fileName; ?>" width="400px" height="50px" style="border: 0px;" scrolling="no"></iframe>
<div id="div_<?php echo $this->context->id; ?>">


</div>
<input type="hidden" value="" name="<?=$this->context->name?>" id="input_<?php echo $this->context->id; ?>">

    <script>
        function uploadProject_<?php echo $this->context->id; ?>(data) {
            $('#div_<?php echo $this->context->id; ?>').html('上传成功：' + data.content.name);
            $('#input_<?php echo $this->context->id; ?>').val(data.content.name);
            <?php if ($this->context->callback){ ?>
            <?php echo $this->context->callback; ?>(data.content.name);
            <?php } ?>
        }

        <?php if ($this->context->imageUrl){ ?>
        (function loadAttachments_<?php echo $this->context->id; ?>() {
            var data = {
                'url' : '<?php echo $this->context->imageUrl; ?>',
            };
            uploadProject_<?php echo $this->context->id; ?>(data);
        })();

        <?php } ?>
    </script>