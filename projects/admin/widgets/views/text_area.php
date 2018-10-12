<div class="form-group">
    <label class="col-lg-<?php echo $this->context->titleLen; ?> control-label"><?php echo $this->context->title; ?></label>
    <div class="col-lg-<?php echo $this->context->contentLen; ?>">
    <textarea
        rows="<?php echo $this->context->rows; ?>"
        cols="<?php echo $this->context->cols; ?>"
        class="form-control valid"
        aria-invalid="false"
        name="<?php echo $this->context->name; ?>" > <?php echo $this->context->content; ?>
 </textarea>
    </div>
    <?php if ($this->context->validation){ ?>
        <span class="field-validation-valid" data-valmsg-for="<?php echo $this->context->name; ?>" data-valmsg-replace="true"></span>
    <?php } ?>
</div>


