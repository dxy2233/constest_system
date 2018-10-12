<!-- 加载所需样式文件 -->
<link href="/static/datePicker/bootstrap-combined.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="/static/datePicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="/static/datePicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/static/datePicker/bootstrap-datetimepicker.zh-CN.js"></script>



<?php echo $this->context->form->field(
    $this->context->model,
    $this->context->attribute,
    [
        'template' => "<div class=\"col-sm-7\"><div class=\"input-append date form_datetime\">{input}<span style=\"height: 35px;width: 28px;margin-left: 0px;\" class=\"add-on\"><i class=\"icon-th\" style=\"margin-top: 5px;\"></i></span></div></div>"]
)
    ->textInput(['value' => $this->context->value, 'maxlength' => true,'readonly' => 'readonly','style'=>'border-radius:0;width: 160px;', 'placeholder' => $this->context->title])
    ->label($this->context->title);
?>

<script type="text/javascript">
    $(".form_datetime").datetimepicker({
        language: 'zh-CN',
        format: "yyyy-MM-dd hh:mm:ss",
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left"
    });
</script>