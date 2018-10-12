<!-- 加载所需样式文件 -->
<link href="/static/datePicker/bootstrap-combined.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="/static/datePicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="/static/datePicker/bootstrap-datetimepicker.min.js"></script>
<!--<script type="text/javascript" src="/static/datePicker/bootstrap-datetimepicker.zh-CN.js"></script>-->



<?php echo $this->context->form->field($this->context->model, $this->context->attribute,[
        'template' => "
<div class='col-sm-7'>
    <div class='input-append form-datetime'>
    {input}
    <span style='height: 35px;width: 28px;margin-left: 0px;' class='add-on'>
        <i data-time-icon='icon-time' data-date-icon='icon-calendar'></i>
    </span>
    </div>
</div>
\n<div class='col-sm-3' >{error}</div>
"]
    )
                ->textInput(['value' => $this->context->value ? $this->context->value : date("H:i:s"), 'dataFormat' => "hh:mm:ss",
                    'maxlength' => true,'readonly' => 'readonly','style'=>'border-radius:0;width: 160px;'])
                ->label($this->context->title);
            ?>

<script type="text/javascript">
    $(function() {
        $('#datetimepicker3').datetimepicker({
            pickDate: false
        });
    });
</script>
<script type="text/javascript">
    $(".form-datetime").datetimepicker({
        // language: 'zh-CN',
        format: "hh:mm:ss",
        autoclose: true,
        pickDate: false,
    });
</script>

