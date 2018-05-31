/**
 * 通用函数
 */
//浏览器快捷键处理
document.addEventListener('keydown', function(e){
    if(e.keyCode == 13){  //enter快捷键
        e.preventDefault();
        return false;
    }
}, false);

//下面用于单或多图片上传预览功能
function setImagePreviews(inputId, divId) {
    var docObj = document.getElementById(inputId);
    var dd = document.getElementById(divId);
    dd.innerHTML = "";
    var fileList = docObj.files;
    for (var i = 0; i < fileList.length; i++) {

        dd.innerHTML += "<div style='float:left; padding: 3px;' > <img id='img" + i + "'  /> </div>";
        var imgObjPreview = document.getElementById("img"+i);
        if (docObj.files && docObj.files[i]) {
            //火狐下，直接设img属性
            imgObjPreview.style.display = 'block';
            imgObjPreview.style.width = '110px';
            imgObjPreview.style.height = '110px';
            //imgObjPreview.src = docObj.files[0].getAsDataURL();
            //火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
            imgObjPreview.src = window.URL.createObjectURL(docObj.files[i]);
        }
        else {
            //IE下，使用滤镜
            docObj.select();
            var imgSrc = document.selection.createRange().text;
            alert(imgSrc)
            var localImagId = document.getElementById("img" + i);
            //必须设置初始大小
            localImagId.style.width = "110px";
            localImagId.style.height = "110px";
            //图片异常的捕捉，防止用户修改后缀来伪造图片
            try {
                localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
            }
            catch (e) {
                alert("您上传的图片格式不正确，请重新选择!");
                return false;
            }
            imgObjPreview.style.display = 'none';
            document.selection.empty();
        }
    }

    return true;
}

/**
 * @todo 获取单张图片上传路径，并设置相应的预览
 * @param object node this对象
 * @param int width 图片宽度px
 * @param int height 图片高度px
 * @param string imgId 预览图片html下div的容器ID
 */
function file_change(node, imgWidth, imgHeight, imgId) {
    var imgURL = "";
    try{
        var file = null;
        if(node.files && node.files[0] ){
            file = node.files[0];
        }else if(node.files && node.files.item(0)) {
            file = node.files.item(0);
        }
        //Firefox 因安全性问题已无法直接通过input[file].value 获取完整的文件路径
        try{
            //Firefox7.0
            imgURL =  file.getAsDataURL();
            //alert("//Firefox7.0"+imgRUL);
        }catch(e){
            //Firefox8.0以上
            imgURL = window.URL.createObjectURL(file);
        }
    }catch(e){
        //支持html5的浏览器,比如高版本的firefox、chrome、ie10
        if (node.files && node.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                imgURL = e.target.result;
            };
            reader.readAsDataURL(node.files[0]);
        }
    }

    var textHtml = "<img src='" + imgURL + "' style='width: " + imgWidth + "px; height: " + imgHeight + "px;' alt='图片不存在'/>";
    $("#" + imgId).html(textHtml);
    //$('#' + imgId).attr('src', imgURL);
}

//浏览器页面返回上一页
function back() {
    window.history.back();
}

//浏览器页面返回下一页
function forward() {
    window.history.forward();
}

/**
 *   @todo 时间戳转换成字符串
 *   @params unix_timestamp    时间戳
 *    @params datesplit   日期分隔符，默认为“-”
 **/
function unix2date(unix_timestamp,datesplit){
    var date = new Date(unix_timestamp*1000);
    var month = date.getMonth()>8 ? date.getMonth()+1 : '0' + (date.getMonth()+1);
    var day = date.getDate()>9 ? date.getDate() : '0' + date.getDate();
    var hours = date.getHours();
    var minutes = date.getMinutes()>9 ? date.getMinutes() : '0' + date.getMinutes();
    if( !datesplit ) datesplit = '-';
    return date.getFullYear() + datesplit + month + datesplit + day + ' ' + hours+':'+minutes;

}

/**
 *   @todo 字符串转换成时间戳
 *   @params datetime    日期
 *    @params datesplit   日期分隔符，默认为“-”
 **/
function date2unix(datetime,datesplit){
    var date = new Date();
    var dt = datetime.split(' ');
    if( !datesplit ) datesplit = '-';
    var fy = dt[0].split(datesplit);
    date.setFullYear(fy[0],fy[1]-1,fy[2]);
    if( dt.length > 1 ){
        var t = dt[1].split(':');
        date.setHours(t[0],t[1]);
    }
    return Math.floor(date.getTime()/1000);
}

/**
 *   @todo 时间差
 *   @params datestr1    日期1
 *   @params datestr2    日期2
 *    @params t   返回时长类型 默认为s  s:秒,m:分钟,h:小时,d:天
 **/
function datediff(datestr1,datestr2,t){
    var diff = date2unix(datestr2) - date2unix(datestr1);
    switch(t){
        case 'm':
            return Math.floor(diff/60);
            break;
        case 'h':
            return Math.floor(diff/3600);
            break;
        case 'd':
            return Math.floor(diff/86400);
            break;
        case 's':
        default:
            return diff;
            break;
    }
}
