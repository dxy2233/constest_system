<div class="form-group">
    <label class="col-lg-<?php echo $this->context->titleLen; ?>  control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <div style="padding-left:0px;">
            <iframe frameborder="0" src="/uploader/default/image-template?context=<?php echo $this->context->context;?>&callback=uploadProject_<?php echo $this->context->id;?>&localImage=true" width="400px" height="50px" style="border: 0px;" scrolling="no"></iframe>
            <?php if ($this->context->requireValidation){ ?>
            <span class="field-validation-valid" dz-for="<?php echo $this->context->name; ?>"></span>
            <?php } ?>
        </div>
        <div id="attachement_container_<?php echo $this->context->id; ?>">
        </div>
        <input type="input" style="width:1px;height:1px;position:relative;left:-1000px;" name="<?php echo $this->context->name; ?>" value="" id="text_<?php echo $this->context->id; ?>" <?php if ($this->context->requireValidation){ ?>dz-validation="true" dz-required="必须上传图片"  <?php }  ?> />

    </div>

    <script>
        function uploadProject_<?php echo $this->context->id; ?>(data) {
            var attachementItem = $('#attachement_container_<?php echo $this->context->id; ?>');
            attachementItem.html('<img style="width:50px;height:50px;" src="' + data.url +  '"/>');
            $('#text_<?php echo $this->context->id; ?>').val(data.url);
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
</div>