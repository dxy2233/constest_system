<div>
    <iframe frameborder="0" src="<?php echo $frameUrl; ?>" height="30px" style="border: 0px;" scrolling="no"></iframe>
</div>

<div id="attachement_container_<?php echo $this->context->id; ?>"
     style="border: 1px solid #f0f0f0;float: left;display: none;clear:both;">
</div>


<script>
    function uploadProject_<?php echo $this->context->id; ?>(data) {
        var attachementItem = $('#attachement_container_<?php echo $this->context->id; ?>');
        attachementItem.html('<a href="' + data.url + '" target="_blank"><img style="width:100px;height:100px;" src="' + data.url + '"/></a>').show();
        $('#text_<?php echo $this->context->id; ?>').val(data.url);
    }

    <?php if ($this->context->imageUrl){ ?>
    (function loadAttachments_ <?php echo $this->context->id; ?>() {
        var data = {
            'url': '<?php echo $this->context->imageUrl; ?>',
        };
        uploadProject_<?php echo $this->context->id; ?>(data);
    })();

    <?php } ?>
</script>