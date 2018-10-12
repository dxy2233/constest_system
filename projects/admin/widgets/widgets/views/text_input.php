<div class="form-group <?php echo $this->context->class ?? ''; ?>">
    <label class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
        <input type="<?php echo $this->context->type; ?>" class="form-control" style="background-color:white;" name="<?php echo $this->context->name; ?>"  value="<?php echo $this->context->value; ?>" <?php echo $this->context->readonly; ?> <?php echo $this->context->validation; ?> />
    </div>
    <?php echo $this->context->unit; ?>
    <?php if ($this->context->validation){ ?>
    <span class="field-validation-valid" data-valmsg-for="<?php echo $this->context->name; ?>" data-valmsg-replace="true"></span>
    <?php } ?>
</div>