<div class="form-group">
    <label class="col-lg-<?php echo $this->context->titleLen; ?>  control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <div style="padding-left:0px;">
            <iframe frameborder="0" src="/uploader/default/<?php echo $this->context->useOss ? 'oss-' : ''; ?>image-template-two?context=<?php echo $this->context->context;?>&callback=uploadProject&id=<?php echo $this->context->id;?>&localImage=false&disabled=<?=$this->context->disabled?>" width="150px" height="70px" style="border: 0px;" scrolling="no"></iframe>
            <?php if ($this->context->requireValidation){ ?>
            <span class="field-validation-valid" data-valmsg-for="<?php echo $this->context->name; ?>" data-valmsg-replace="true"></span>
            <?php } ?>
        </div>
        <div id="attachement_container_<?php echo $this->context->id; ?>" style="border: 1px dashed gray;float: left;display: none;">
        </div>
        <img class="attachement_container_<?php echo $this->context->id; ?>" style="width:100px;height:100px;" src="{{property.content}}"/>
        <input type="input" style="width:1px;height:1px;position:relative;left:-1000px;" name="<?php echo $this->context->name; ?>" value="{{property.content}}" id="text_<?php echo $this->context->id; ?>" <?php if ($this->context->requireValidation){ ?>data-val="true" data-val-required="必须上传图片"  <?php }  ?> />

    </div>

    <script>


        function uploadProject(id, data) {
            $('.attachement_container_'+id).remove();
            var attachementItem = $('#attachement_container_'+id);
            attachementItem.html('<a href="' + data.url + '" target="_blank">' +
                '<img style="width:100px;height:100px;" src="' + data.url +  '"/></a>').show();
            $('#text_'+id).val(data.url);


        }





    </script>
</div>