<div class="form-group">
    <label class="col-lg-<?php echo $this->context->titleLen; ?>  control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <div style="padding-left:0px;">
            <iframe frameborder="0" src="/uploader/default/<?php echo $this->context->useOss ? 'oss-' : ''; ?>image-template?context=<?php echo $this->context->context;?>&callback=uploadProject_<?php echo $this->context->id;?>&localImage=false&disabled=<?=$this->context->disabled?>&hasImage=<?= $this->context->imageUrl ? true : false;?>" width="150px" height="70px" style="border: 0px;" scrolling="no"></iframe>
            <?php if ($this->context->requireValidation){ ?>
                <span class="field-validation-valid" data-valmsg-for="<?php echo $this->context->name; ?>" data-valmsg-replace="true"></span>
            <?php } ?>
        </div>
        <div id="attachement_container_<?php echo $this->context->id; ?>" style="border: 1px dashed gray;float: left;display: none;">
        </div>
        <input type="input" style="width:1px;height:1px;position:relative;left:-1000px;" name="<?php echo $this->context->name; ?>" value="" id="text_<?php echo $this->context->id; ?>" <?php if ($this->context->requireValidation){ ?>data-val="true" data-val-required="必须上传图片"  <?php }  ?> />

    </div>

    <script>
        function uploadProject_<?php echo $this->context->id; ?>(data) {
            var attachementItem = $('#attachement_container_<?php echo $this->context->id; ?>');
            attachementItem.html('<a href="' + data.url + '" target="_blank"><img style="width:100px;height:100px;" src="' + data.url +  '"/></a>').show();
            $('#text_<?php echo $this->context->id; ?>').val(data.url);
            <?php if ($this->context->callback) { ?>
            <?php echo $this->context->callback ?>(data.url);
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
</div>