<div class="form-group">
    <label class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <?php foreach ($this->context->items as $itemValue => $itemTitle) { ?>
            <label class="radio-inline">
                <input type="radio" name="<?php echo $this->context->name; ?>"
                       value="<?php echo $itemValue; ?>"><?php echo $itemTitle; ?>
            </label>
        <?php } ?>
    </div>
</div>
    <script>
        $('input[type=radio][name=<?php echo $this->context->name; ?>][value=<?php echo $this->context->selectedValue; ?>]').attr('checked', 'checked');
    </script>
