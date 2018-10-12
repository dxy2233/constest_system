<?php
namespace common\components;
/**
 *
 * User: simonzhang
 * Date: 15/3/12
 * Time: 上午9:30
 */

class HtmlHelper {

    /**
     *
     * 日期选择
     *
     * @param $params
     * @return mixed|string
     */
    public static function getDatepickerInput($params) {
        $defaultParams = [
            'title'         => '',
            'name'          => 'input',
            'value'         => '',
            'unit'          => '',
            'readonly'      => false,
            'titleLen'      => 1,
            'contentLen'    => 3,
            'validation'    => '',
            'validationMsg' => '',
            'type'          => 'text'
        ];

        $defaultParams = array_merge($defaultParams, $params);
        $template = <<<EOF
          <div class="form-group" id="item_div_%name">
             <label class="col-lg-%tLen control-label">%title</label>
             <div class="col-lg-%cLen">
                <input name="%input_name" id="%id" size="16" type="text" value="%default_value" class="form_datetime form-control" style="background-color:white;"  readonly %validation />
            </div>
            %unit
             %validateMsg
        </div>
    <script>
        $(function(){
            $('#%id').datepicker({
                format: 'yyyy-mm-dd',
                language: 'zh-CN',
                startDate: '%today'


            }).on('changeDate', function(ev){
                $('#%id').datepicker('hide');
            });
        });
    </script>
EOF;
        $template = str_replace('%id', 'auto_gen_input_' . time() . rand(1, 9999), $template);
        $template = str_replace('%title', $defaultParams['title'], $template);
        if (empty($defaultParams['value'])) {
            $template = str_replace('%default_value', '', $template);
        } else {
            $template = str_replace('%default_value', $defaultParams['value'], $template);
        }
        $template = str_replace('%input_name', $defaultParams['name'], $template);
        $template = str_replace('%tLen', $defaultParams['titleLen'], $template);
        $template = str_replace('%cLen', $defaultParams['contentLen'], $template);
        $template = str_replace('%today', date('Y-m-d', strtotime('+1 day')), $template);

        if (empty($defaultParams['unit'])) {
            $template = str_replace('%unit', '', $template);
        } else {
            $template = str_replace('%unit', sprintf('<span class="g-unit">%s</span>', $defaultParams['unit']), $template);
        }

        $template = str_replace('%validation', $defaultParams['validation'], $template);
        $template = str_replace('%validateMsg', sprintf('<span class="field-validation-valid" data-valmsg-for="%s" data-valmsg-replace="true">%s</span>', $defaultParams['name'], $defaultParams['validationMsg']), $template);

        return $template;

    }


    /**
     * 普通输入框
     *
     * @param $params
     * @return mixed|string
     */
    public static function getNormalInput($params) {
        //$title, $inputName, $value, $unit = '', $readonly = false, $titleLen = 1, $contentLen = 5, $validation = '') {

        $defaultParams = [
            'title'         => '',
            'name'          => 'input',
            'value'         => '',
            'unit'          => '',
            'readonly'      => false,
            'titleLen'      => 1,
            'contentLen'      => 3,
            'validation'    => '',
            'validationMsg' => '',
            'type'          => 'text',
            'class'         => '',


        ];

        $defaultParams = array_merge($defaultParams, $params);

        $template = <<<EOF
         <div class="form-group %class" id="item_div_%name">
             <label class="col-lg-%tLen control-label">%title</label>
             <div class="col-lg-%cLen">
             <input type="%type" class="form-control" style="background-color:white;" id="input_%name" name="%name"  value="%value" %readonly %validation />
             </div>
             %unit
             %validateMsg
        </div>
EOF;

        $template = str_replace('%title', $defaultParams['title'], $template);
        $template = str_replace('%class', $defaultParams['class'], $template);
        $template = str_replace('%type', $defaultParams['type'], $template);
        $template = str_replace('%tLen', $defaultParams['titleLen'], $template);
        $template = str_replace('%cLen', $defaultParams['contentLen'], $template);
        $template = str_replace('%name', $defaultParams['name'], $template);
        $template = str_replace('%value', $defaultParams['value'], $template);
        if (empty($defaultParams['unit'])) {
            $template = str_replace('%unit', '', $template);
        } else {
            $template = str_replace('%unit', sprintf('<span class="g-unit">%s</span>', $defaultParams['unit']), $template);
        }

        if ($defaultParams['readonly']) {
            $template = str_replace('%readonly', ' readonly ', $template);
        } else {
            $template = str_replace('%readonly', '', $template);
        }

        $template = str_replace('%validation', $defaultParams['validation'], $template);
        $template = str_replace('%validateMsg', sprintf('<span class="field-validation-valid" data-valmsg-for="%s" data-valmsg-replace="true">%s</span>', $defaultParams['name'], $defaultParams['validationMsg']), $template);

        return $template;

    }

    /**
     * 百度HTML Editor
     *
     * @param $title
     * @param $inputName
     * @param string $content
     * @return mixed|string
     */
    public static function getRichEditor($title, $inputName, $content = '', $context) {
        $template = <<<EOF
         <div class="form-group form-group-visible">
            <label class="col-lg-1 control-label">$title</label>
            <div class="col-lg-10">
                 <textarea id="editor_%editorId" class="" name="%inputName" style="width: 100%;">%content</textarea>
                    <script>
                    tinymce.init({
  selector: "#editor_%editorId",
  language: 'zh_CN',
  height: 500,
  plugins: [
    "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
  ],

  toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify upload | forecolor backcolor styleselect formatselect fontselect fontsizeselect",
  toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview",
  toolbar3: "table | hr removeformat | subscript superscript | charmap | fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking pagebreak restoredraft",

  menubar: false,
  toolbar_items_size: 'small',

  style_formats: [{
    title: 'Bold text',
    inline: 'b'
  }, {
    title: 'Red text',
    inline: 'span',
    styles: {
      color: '#ff0000'
    }
  }, {
    title: 'Red header',
    block: 'h1',
    styles: {
      color: '#ff0000'
    }
  }, {
    title: 'Example 1',
    inline: 'span',
    classes: 'example1'
  }, {
    title: 'Example 2',
    inline: 'span',
    classes: 'example2'
  }, {
    title: 'Table styles'
  }, {
    title: 'Table row 1',
    selector: 'tr',
    classes: 'tablerow1'
  }],

  templates: [{
    title: 'Test template 1',
    content: 'Test 1'
  }, {
    title: 'Test template 2',
    content: 'Test 2'
  }],
  
   setup: function (editor) {
    editor.addButton('upload', {
      text: '上传图片',
      icon: false,
      onclick: function () {
      $('#upload_modal_%editorId').modal();
      }
    });
  },
});
                    </script>
            </div>
            <div class="form-group-clear"></div>
         </div>
         
<!-- Modal -->
<div class="modal fade" id="upload_modal_%editorId" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">上传图片</h4>
            </div>
            <div class="modal-body" style="width:1100px;">
               %upcodes
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="insert_pic_%editorId();">确定</button>
            </div>
        </div>
    </div>
</div>
<script>
function insert_pic_%editorId() {
tinymce.get('editor_%editorId').insertContent('<img src="' + $('#upload_modal_%editorId img').attr('src') + '"/>');
 $('#upload_modal_%editorId').modal('hide');
}
</script>
EOF;

        $template = str_replace('%title', $title, $template);
        $template = str_replace('%context', $context, $template);
        $template = str_replace('%inputName', $inputName, $template);
        $template = str_replace('%content', $content, $template);
        $template = str_replace('%qhost', \Yii::$app->params['qiniuDomain'], $template);
        $template = str_replace('%editorId', rand(1, 99999) . time(), $template);
        $template = str_replace('%upcodes', HtmlHelper::getSingleImageAttachmentUploadHtml($title,
            $inputName . '_' . rand(1, 999999), null, $context), $template);


        return $template;
    }

    /**
     * 普通的多行文本框
     *
     * @param $title
     * @param $inputName
     * @param string $content
     * @return mixed|string
     */
    public static function getTextArea($params) {

        $defaultParams = [
            'title'         => '',
            'name'          => 'input',
            'value'         => '',
            'unit'          => '',
            'readonly'      => false,
            'titleLen'      => 1,
            'validation'    => '',
            'validationMsg' => '',
            'type'          => 'text',
            'rows'          => 10,
            'cols'          => 10,
            'id'          => 10
        ];

        $defaultParams = array_merge($defaultParams, $params);

        $template = <<<EOF
         <div class="form-group">
            <label class="col-lg-%tlen control-label">%title</label>
            <div class="col-lg-10">
                 <textarea rows="%rows" cols="%cols" class="form-control" name="%inputName"  id="%id"  %validation>%content</textarea>
             </div>
            %validateMsg
         </div>
EOF;

        $template = str_replace('%title', $defaultParams['title'], $template);
        $template = str_replace('%tlen', $defaultParams['titleLen'], $template);
        $template = str_replace('%inputName', $defaultParams['name'], $template);
        $template = str_replace('%content', $defaultParams['value'], $template);
        $template = str_replace('%rows', $defaultParams['rows'], $template);
        $template = str_replace('%cols', $defaultParams['cols'], $template);
        $template = str_replace('%id', $defaultParams['id'], $template);

        $template = str_replace('%validation', $defaultParams['validation'], $template);
        $template = str_replace('%validateMsg', sprintf('<span class="field-validation-valid" data-valmsg-for="%s" data-valmsg-replace="true">%s</span>', $defaultParams['name'], $defaultParams['validationMsg']), $template);


        return $template;
    }


    public static function getLeftMenu($menus) {
        $template = <<<EOF
       <div class="left-menu">
    <div class="left-menu-content" id="left_menu">
        %menus
    </div>
</div>
EOF;

        $menuTemplate = '';
        foreach ($menus as $menu => $content) {
            if (!is_array($content)) {
                $menuTemplate .= sprintf('<a href="%s" class="left-menu-1">%s</a>',
                    $content, $menu);
            } else {
                if ($content['url'] == '#') {
                    $menuTemplate .= sprintf('<a href="%s" class="left-menu-1">%s</a>',
                        'javascript:void(0);', $menu);
                } else {
                    $menuTemplate .= sprintf('<a href="%s" class="left-menu-1">%s</a>',
                        '', $menu);
                }
                foreach ($content['subMenus'] as $subName => $subUrl) {
                    $menuTemplate .= sprintf('<a href="%s" class="left-menu-2">%s</a>',
                        $subUrl, $subName);
                }
            }
        }

        $template = str_replace('%menus', $menuTemplate, $template);

        return $template;

    }


    /**
     * 图片上传的模板
     *
     * @return mixed|string
     */
    public static function getAttachmentUploadTemplate() {

        $template = <<<EOF
        <div style="line-height:50px;width:450px; display: none;clear:both;" id="attachement_template" class="form-inline upload_img_result">
            <label style="font-weight:normal;">名称:</label>
            <span class="space-5"></span>
            <input type="text" name="attachmentNames[]" class="form-control" />
            <span class="space-5"></span>
            <a href="" target="_blank" id="file_link"><img src="" style="width: 50px;height:50px;" /></a>
            <span class="space-5"></span>
            <input type="hidden" name="attachmentUrls[]" />
            <a href="#" onclick="deleteAttachement(this);return false;">删除</a>
        </div>
         <div style="line-height:50px;width:450px; display: none;clear:both;" id="attachement_template_others" class="form-inline upload_img_result">
            <label style="font-weight:normal;">名称:</label>
            <span class="space-5"></span>
            <input type="text" name="attachmentNames[]" class="form-control" />
            <span class="space-5"></span>
            <a href="" target="_blank" id="file_link"></a>
            <span class="space-5"></span>
            <input type="hidden" name="attachmentUrls[]" />
            <a href="#" onclick="deleteAttachement(this);return false;">删除</a>
        </div>
EOF;


        return $template;
    }


    /**
     * 多图片上传
     * @param $title
     * @param $name
     * @param $attachments
     * @param $context
     * @return string
     */
    public static function getImageAttachmentUploadHtml($title, $name, $attachments, $context) {

        $template = <<<EOF
        <div class="form-group">
            <label class="col-lg-1 control-label">#title#</label>
           <div class="col-lg-5">
                <div>
                    <iframe src="/uploader/default/image-template?context=#context#&callback=uploadProject_#containerId#&localImage=false" width="400px" height="50px" style="border: 0px;" scrolling="no" frameborder="0"></iframe>
                    <input type="input" style="width:0px;height:0px;position: relative;left: -1000px;" name="attachments" data-val="true" data-val-attachments="必须上传相关附件">
                    <span class="field-validation-valid" data-valmsg-for="attachments" data-valmsg-replace="true"></span>
                </div>
                <div style="clear:both;" id="attachment_container_#containerId#">
            </div>
        </div>

        <script>
            function uploadProject_#containerId#(data) {
              var index1 = data.url.lastIndexOf(".");
                var index2 = data.url.length;
                var ext = data.url.substring(index1, index2);
                var type = ext;
                if (type == '.xlsx' || type == '.xls' || type == '.pdf') {
                     var attachementItem = $('#attachement_template_others').clone();
                    attachementItem.removeAttr('id').show();
                    attachementItem.find('input').eq(0).attr('name', "#name#_names[]").val(data.originalName.split('.')[0]);
                    attachementItem.find('#file_link').attr('href', data.url);
                    attachementItem.find('#file_link').text(data.originalName);
                    attachementItem.find('input').eq(1).attr('name', "#name#_urls[]").val(data.url);
                    attachementItem.appendTo($('#attachment_container_#containerId#'));
                } else {
                     var attachementItem = $('#attachement_template').clone();
                    attachementItem.removeAttr('id').show();
                    attachementItem.find('input').eq(0).attr('name', "#name#_names[]").val(data.originalName.split('.')[0]);
                    attachementItem.find('img').attr('src', data.url);
                    attachementItem.find('a').attr('href', data.url);
                    attachementItem.find('input').eq(1).attr('name', "#name#_urls[]").val(data.url);
                    attachementItem.appendTo($('#attachment_container_#containerId#'));
                }

            }
            function deleteAttachement(self) {
                $(self).parent().remove();
            }
            #loadtemplate#
        </script>
        </div>
EOF;


        if (!empty($attachments)) {
            $loadTemplate = <<<EOF
                function loadAttachments_#containerId#() {
                    var attachments = %s;
                    for (var index in attachments) {
                        var data = {
                            'originalName' : attachments[index]['name'],
                            'url' : attachments[index]['url'],
                            };
                        uploadProject_#containerId#(data);
                    }
                }

                loadAttachments_#containerId#();
EOF;
            $loadTemplate = sprintf($loadTemplate, json_encode($attachments));
        } else {
            $loadTemplate = '';
        }

        return str_replace(
            ['#title#', '#context#', '#loadtemplate#', '#name#', '#containerId#'],
            [$title, $context, $loadTemplate, $name, md5($title)],
            $template
        );


    }

    public static function getHorizontalInfo($id, $title, $value) {
        $template = <<<EOF
             <div class="form-group">
                <label class="col-lg-2 control-label-title">%s</label>
                <label class="col-lg-5 control-label-content" id="%s">%s</label>
            </div>
EOF;

        return sprintf($template, $title, $id, $value);

    }


    /**
     *
     * 错误提示块
     * @param $inputName
     * @return string
     */
    public static function getErrorLabel($inputName) {
        return sprintf('<span class="field-validation-error input-tailor" data-valmsg-for="%s" data-valmsg-replace="true"></span>',
            $inputName);
    }


    /**
     * 单个图片上传
     * @param $title
     * @param $name
     * @param $attachment
     * @param $context
     * @param $required
     * @return string
     */
    public static function getSingleImageAttachmentUploadHtml($title, $name, $attachment, $context, $required = false) {

        $template = <<<EOF
<div class="form-group">
        <label class="col-lg-1 control-label">#title#</label>
        <div class="col-lg-4">
            <div style="padding-left:0px;">
                <iframe frameborder="0" src="/uploader/default/image-template?context=#context#&callback=uploadProject_#containerId#&localImage=false" width="400px" height="50px" style="border: 0px;" scrolling="no"></iframe>
                #validation#
            </div>
            <div id="attachement_container_#containerId#">
            </div>
            <input type="input" style="width:1px;height:1px;position:relative;left:-1000px;" name="#name#" value="" id="text_#containerId#" #validation_input# />
            
        </div>

        <script>
            function uploadProject_#containerId#(data) {
                var attachementItem = $('#attachement_container_#containerId#');
                attachementItem.html('<img style="width:50px;height:50px;" src="' + data.url +  '"/>');
                $('#text_#containerId#').val(data.url);
            }
            #loadtemplate#
        </script>
    </div>
EOF;

        if ($required) {
            $template = str_replace('#validation#', '<span class="field-validation-valid" data-valmsg-for="#name#" data-valmsg-replace="true"></span>', $template);
            $template = str_replace('#validation_input#', 'data-val="true" data-val-required="必须上传图片"', $template);
        } else {
            $template = str_replace('#validation#', '', $template);
            $template = str_replace('#validation_input#', '', $template);
        }

        if (!empty($attachment)) {
            $loadTemplate = <<<EOF
                function loadAttachments_#containerId#() {
                    var data = {
                        'url' : '%s',
                        };
                    uploadProject_#containerId#(data);
                }

                loadAttachments_#containerId#();
EOF;
            $loadTemplate = sprintf($loadTemplate, $attachment);
        } else {
            $loadTemplate = '';
        }



        return str_replace(
            ['#title#', '#context#', '#loadtemplate#', '#name#', '#containerId#'],
            [$title, $context, $loadTemplate, $name, md5($title)],
            $template
        );
    }


    /**
     * 普通选择框
     *
     * @param $params
     * @return mixed|string
     */
    public static function getSelect($params) {

        $defaultParams = [
            'title'         => '',
            'name'          => 'input',
            'value'         => '',
            'unit'          => '',
            'readonly'      => false,
            'titleLen'      => 1,
            'validation'    => '',
            'validationMsg' => '',
            'type'          => 'select'
        ];

        $defaultParams = array_merge($defaultParams, $params);

        $template = <<<EOF
         <div class="form-group" id="item_div_%name">
             <label class="col-lg-%tLen control-label">%title</label>
             <div class="col-lg-3">
                 <select name="%name" class="form-control">
                    %options
                 </select>
             </div>
             %unit
        </div>
EOF;

        $template = str_replace('%title', $defaultParams['title'], $template);
        $template = str_replace('%type', $defaultParams['type'], $template);
        $template = str_replace('%tLen', $defaultParams['titleLen'], $template);
        $template = str_replace('%name', $defaultParams['name'], $template);
        if (empty($defaultParams['unit'])) {
            $template = str_replace('%unit', '', $template);
        } else {
            $template = str_replace('%unit', sprintf('<span>%s</span>', $defaultParams['unit']), $template);
        }

        $optionStr = '';
        foreach ($defaultParams['items'] as $k => $v) {
            if($defaultParams['value'] == $k){
                $optionStr .= '<option value="' . $k . '" selected="true">' . $v . '</option>';
            }else{
                $optionStr .= '<option value="' . $k . '" >' . $v . '</option>';
            }

        }
        $template = str_replace('%options', $optionStr, $template);

        return $template;

    }



    /**
     * 联动选择
     *
     * @param $params
     * @return mixed|string
     */
    public static function getSelectS($selectArr,$params) {
        /*
         * select 默认样式
         * */
        $defaultParams = [
            'title'         => '',
            'name'          => '',
            'value'         => '',
            'unit'          => '<span></span>',
            'id'            => 'id',
            'readonly'      => false,
            'titleLen'      => 1,
            'validation'    => '',
            'validationMsg' => '',
            'type'          => 'select',
            'width'         =>'100',
            'contentLen'      =>3,
            'class' =>'',
            'display'=>''
        ];
            $defaultParams = array_merge($defaultParams, $params);
            $template_row = ' <div class="form-group " id="item_div_%name">
                            <label class="col-lg-%tLen control-label ">%title</label>';
            $template_row = str_replace('%title', $defaultParams['title'], $template_row);
            $template_row = str_replace('%tLen', $defaultParams['titleLen'], $template_row);
            $template_row = str_replace('%name', $defaultParams['name'], $template_row);
            $templates = '';

            foreach($selectArr as $select){
            $select = array_merge($defaultParams, $select);

            $template = ' <div class="col-lg-%contentLen %class" style="display:%display;">
                 <select id="%id" class="form-control select_province" style="width: %width%" name="%name">
                    %options
                 </select>
                  %unit
             </div>';

            $template = str_replace('%id', $select['id'], $template);
            $template = str_replace('%name', $select['name'], $template);
            $template = str_replace('%class', $select['class'], $template);
            $template = str_replace('%contentLen', $select['contentLen'], $template);
            $template = str_replace('%display', $select['display'], $template);
            $template = str_replace('%unit', $select['unit'], $template);
            $template = str_replace('%width', $select['width'], $template);
            $optionStr = '';

            if(isset($select['default'])){
                $optionStr .= '<option value=>'.$select['default'].'</option>';
            }

            if(isset($select['items'])){
                foreach ($select['items'] as $k => $v) {
                    if($select['value'] == $k){
                        $optionStr .= '<option value="' . $k . '" selected="true">' . $v . '</option>';
                    }else{
                        $optionStr .= '<option value="' . $k . '" >' . $v . '</option>';
                    }
                }
                $template = str_replace('%options', $optionStr, $template);
            }
                $templates .= $template;
            }

            $template_row .= $templates.'</div>';
            return $template_row;
    }


}