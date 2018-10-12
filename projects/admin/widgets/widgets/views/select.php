<div class="form-group">
    <label class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <select class="form-control" name="<?php echo $this->context->name; ?>" id="<?php echo $this->context->id; ?>">
            <?php
            foreach ($this->context->items as $itemValue => $itemTitle){ ?>
                <option value="<?php echo $itemValue; ?>"><?php echo $itemTitle; ?></option>
            <?php } ?>
        </select>
    </div>

    <?php if ($this->context->selectedValue){ ?>
    <script>
        $('#<?php echo $this->context->id; ?>').val(<?php echo $this->context->selectedValue; ?>);
    </script>
    <?php } ?>
</div>