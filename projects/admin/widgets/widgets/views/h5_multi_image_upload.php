<script type="text/javascript" src="/static/js/zepto.js"></script>
<style>
    .bgPhoto-uploadBox-cont {
        margin-top: 0.8rem;
        padding: 0.4rem 0.267rem 0.267rem;
        background: #fff;
        overflow: hidden;
    }

    .bgPhoto-uploadBox-cont > .bgPhoto-upload-img:nth-child(4n) {
        margin-right: 0;
    }

    .bgPhoto-upload-img > img {
        background-size: cover;
        width: 100%;
        height: 100%;
    }

    .bgPhoto-upload-img {
        width: 2.267rem;
        height: 2.267rem;
        border-radius: 5px;
        overflow: hidden;
        float: left;
        margin-bottom: 0.133rem;
        margin-right: 0.04rem;
    }

    .g-s-b .g-sure-btn {
        width: 100%;
        height: 1.2rem;
        background-color: #ff5900;
        line-height: 1.2rem;
        font-size: 0.426rem;
        color: #fff;
        text-align: center;
        display: block;
        position: fixed;
        border: none;
        bottom: 0;
        z-index: 5;
    }

    .bgPhoto-upload-btn {
        border: 1px solid #ebebeb;
        box-sizing: border-box;
        position: relative;
    }

    .p-backstage .bgPhoto-upload-btn:before {
        content: "";
        width: 0.907rem;
        height: 0.747rem;
        display: block;

        position: absolute;
        top: 0.667rem;
        left: 0.6rem;
        background-image: url("/static/image/icon_photo.png");
        background-size: cover;
    }

    .bgPhoto-upload-img{
        position: relative;
    }


    .imgDefault:before{
        /*content: url("/static/image/submit-orders-gouxuan.png");*/
        content:'';
        background-image:url("/static/image/submit-orders-gouxuan.png");
        position: absolute;
        top: 0;
        right: 0;
        width: 0.4rem;
        height: 0.4rem;
        background-size:cover ;
        display: block;
    }

    .isSelected:before{
        /*content: url("/static/image/submit-orders-gouxuanhou.png");*/
        content:"";
        background-image: url("/static/image/submit-orders-gouxuanhou.png");
        position: absolute;
        top: 0;
        right: 0;
        width: 0.4rem;
        height: 0.4rem;
        background-size:cover ;
        display: block;
    }

    .hg-center{
        float: left;
    }

    .edt-hg{
        font-size: 0.373rem;
        line-height: 1.173rem;
        color: #ff5900;
        float: left;
    }
    .p-backstage{
        margin-left:-0.1333rem ;
    }

</style>
<div class="g-header clearfix">
    <span class="g-icon-back-box" id="goBack">
        <i class="g-icon-back"></i>
    </span>
    <div class="h-center hg-center"><?= $this->title ?></div>
    <div class="edt-hg" ></div>
</div>
<div class="p-backstage">
    <div class="bgPhoto-uploadBox">
        <div class="bgPhoto-uploadBox-cont  uploadBox<?php echo $this->context->id; ?>">
            <div>
                <div style="clear:both;text-align: left;" class="bgPhoto-upload-img bgPhoto-upload-btn">
                    <iframe src="/uploader/default/h5-upload-image?context=<?php echo $this->context->context; ?>&callback=uploadProject_<?php echo $this->context->id; ?>&localImage=false&maxFiles=<?php echo $this->context->maxFiles; ?>"
                            width="100%"
                            height="100%"
                            style="border: 0px;"
                            scrolling="no"
                            frameborder="0"
                    ></iframe>
                    <input type="input" style="width:0px;height:0px;position: relative;left: -1000px;"
                           name="attachments"
                           data-val="true" data-val-attachments="必须上传相关附件">
                    <span class="field-validation-valid" data-valmsg-for="attachments"
                          data-valmsg-replace="true"></span>
                </div>
            </div>
        </div>
        <input type="hidden" name="datas" id="datas<?php echo $this->context->id; ?>">
        <div class="g-s-b">
            <?php if ($this->context->callBackFun): ?>
                <input type="button" class="g-sure-btn" onclick="returnInfo(<?=$this->context->params?>)" value="完成">
            <?php else: ?>
                <input type="button" class="g-sure-btn" onclick="sumbitData()" value="完成">
            <?php endif; ?>
        </div>
    </div>
</div>
<script>




    $(document).on('click','.bgPhoto-upload-img',function () {
        var hasSelected = false;
        if($(this).hasClass('imgDefault')){
            $(this).removeClass('imgDefault').addClass('isSelected');

        }else{
            $(this).removeClass('isSelected').addClass('imgDefault');
        }
        $.each($('.bgPhoto-upload-img'),function (key,value) {
            if($(this).hasClass('isSelected')){
                hasSelected = true;
                return;
            }
        })
        if(hasSelected){
            $('.edt-hg').addClass('canEdit').text('删除');
        }else{
            $('.edt-hg').text('');
        }

    })

    $(document).on('click','.canEdit',function () {
        $.each($('.isSelected'),function (key,value) {
            $(this).remove();
        })
    })


    function getUploadData() {
        var data = [];
        $.each($('.nowUploadImg'),function (key,value) {
            data.push($(this).attr('src'))
        })
        return data;
    }





    function uploadProject_<?php echo $this->context->id; ?>(data) {
        var imgData = getUploadData();
        if (imgData.length >= <?php echo $this->context->maxFiles; ?>) {
            $.dz.DG.errorMessage('最多只能上传<?php echo $this->context->maxFiles; ?>张图片');
            return false;
        }
        var index1 = data.url.lastIndexOf(".");
        var index2 = data.url.length;
        var ext = data.url.substring(index1, index2);
        var type = ext;
        if (type == '.xlsx' || type == '.xls' || type == '.pdf') {
            var attachementItem = $('#attachement_template_others_<?php echo $this->context->id; ?>').clone();
            attachementItem.removeAttr('id').show();
            attachementItem.find('input').eq(0).attr('name', "<?php echo $this->context->name; ?>_names[]").val(data.originalName.split('.')[0]);
            attachementItem.find('#file_link').attr('href', data.url);
            attachementItem.find('#file_link').text(data.originalName);
            attachementItem.find('input').eq(1).attr('name', "<?php echo $this->context->name; ?>_urls[]").val(data.url);
            attachementItem.appendTo($('#attachment_container_<?php echo $this->context->id; ?>'));
        } else {
            $html = '<div class="bgPhoto-upload-img imgDefault">' +
                '<img class="nowUploadImg" src="' + data.url + '" alt="">' +
                '</div>';
            $.each($('.uploadBox<?php echo $this->context->id; ?>'),function (key,value) {
                if(key == 1){
                    $(this).append($html);
                }
            })

        }
    }



    function sumbitData() {

            var data = getUploadData();
            if(data.length == 0){
                $.dz.DG.close();
                setTimeout(function () {
                    $.dz.refreshPage();
                },500)

                return;
            }else{
            $.post("<?= $this->context->callBackUrl?>",
            {
                datas: data.join(' '),
            },
            function (data) {
                var returnInfo = $.dz.evalData(data);
                if (returnInfo.code == 0) {
                    $.dz.DG.close();
                    $.dz.refreshPage();
                } else {
                    $.dz.DG.errorMessage(returnInfo.msg);
                }
                });
            }

    }


    /*回调图片上传数据*/
    function returnInfo($params) {
        var data = getUploadData();
        if(data.length == 0){
            $.dz.DG.close();
            setTimeout(function () {
                $.dz.refreshPage();
            },500)
            return;
        }else{
            <?php if($this->context->params): ?>
            <?=$this->context->callBackFun?>(data,$params);
            <?php else:?>
            <?=$this->context->callBackFun?>(data);
            <?php endif;?>
            $.dz.DG.close();

        }

    }
</script>
