Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
function Car() {
    var self = this;
    var page_path = _prePath + "page/";
    var img_path = _prePath + "assets/images/";
    var this_error = null;
    var car_folder = '';

    this.submit = (el) =>{
        let parent = el.parents('#modal_error')
        let type = el.attr('mv-type');
        let typeError = parent.find('#error_type').val();
        let err_other = $("#err_other").length > 0 ? $("#err_other").val() : '';
        let car_code = $('#car_code').length > 0 ? $('#car_code').val() : $("#vin-car-change").val();
        if(type == 'delete' && self.this_error != null){
            if(self.this_error.hasClass('none_choice_error')){
                self.this_error.remove();
                $("#modal_error").modal('hide');
                return false;
            }
        }

        if(typeError == '-1' || typeError == null){
            parent.find("#error_type").css({'border-color' : 'red'})
            return false;
        }

        if(typeError == err_option_other_id && err_other == ''){
            parent.find("#err_other").css({'border-color' : 'red'})
            return false;
        }

        if(car_code.length < length_car_code){
            parent.modal('hide');
            $('#car_code').css({'border-color' : 'red'})
            return false;
        }
        let result = null;
        if(type == 'add'){
            result = self.setAllPendingError(typeError, err_other);
        }
        else if(type == 'edit' || type == 'delete'){
            result = {
                'toadoX' : el.attr('mv-x'),
                'toadoY' : el.attr('mv-y'),
                'type_error' : typeError,
                'err_other' : el.attr('mv-errother')
            }
        }
        $.ajax({
            url : page_path + 'submitCar.php',
            type : "post",
            dataType : 'json',
            data : {error_code : car_code, type : type, error : result},
            success : function (result) {
                self.cbSubmit(result, type);
            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete : function () {
                $("#modal_error").modal('hide');
            }
        })
    }
    this.passQC1K
    this.cbSubmit = (result, type) => {
        if(type == 'delete'){
            self.this_error.remove();
            self.this_error = null;
        }
        if(type == 'edit'){
            $(this_error)
            //self.this_error.attr('mv-error', result.data.error.type_error);
            //if(result.data.error.type_error != '1' && result.data.error.type_error != '2'){
            //    self.this_error.addClass('other_type_error');
            //}else{
            //    self.this_error.removeClass('other_type_error');
            //}
        }
		//self.loadError(result.code, true, false);
    }
    this.setAllPendingError = (typeError, err_other = '') =>{
        let result = [];
        $("#bodyer").find(".__img").find('.none_choice_error').each(function () {
            let temp = {
                'toadoX' : $(this).attr('mv-x'),
                'toadoY' : $(this).attr('mv-y'),
                'error_position' : $(this).attr('mv-position'),
                'type_error' : typeError,
                'err_other' : err_other
            }
            if(typeError != '1' && typeError != '2'){
                $(this).addClass('other_type_error');
            }
            result.push(temp);
            $(this).attr('mv-error', typeError).attr('mv-errother',err_other).removeClass('none_choice_error');
        })
        return result;
    }
    this.loadImg = (code,loadError = false, sealer = false) => {
        $code_in = code;
        if(loadError){
            $code_in = code.substring(0,length_car_vin);
        }
        $.ajax({
            url : page_path + 'loadImg.php',
            type : 'post',
            dataType : 'json',
            data : {
                code : $code_in,
                car_code : code,
                sealer : sealer ? 'true' : 'false'
            },
            success : function (result) {
                if(result.code == 200){
                    self.cbLoadImg(result,code,loadError, sealer);
                }
                else{
                    console.log(result.message);
                }
                if(result.sttInsert == 0){
                    alert('insert data error. please contact to admin');
                }
            },
            error : function(error){
                console.log(error.responseText);
            }
        })
    }
    this.cbLoadImg = (result,code,loadError, sealer = false) => {
        let images = result.data;
        let img_append = '';
        let code_folder = loadError ? code.substring(0,length_car_vin) : code;
        for(let i in images){
            let url = img_path + result.folder.toUpperCase() + '/' + (sealer ? "SEALER" : position) + '/' + images[i].image;
            img_append += `
                <div class="__box_img">
                    <div class="__title_header text-center"><h4></h4></div>
                    <div class="__img mt-3 mb-3">
                        <img src="` + url + `" alt="` + ((images[i].image.split('.'))[0]) + `" class="w-100">
                    </div>
                    <div class="__title_img text-center">
                          <h4>` + self.translate((images[i].image.split('.'))[0]) + `</h4>              
                    </div>
                </div>
            `;
        }
        self.emptyImg();
        $(".car_area").append(img_append);
        setTimeout(function () {
            self.setOwlCarousel();
        },500);
        if(loadError){
            self.loadError(code, sealer);
        }
        self.car_folder = result.folder.toUpperCase();
    }
    this.loadError = (code, sealer = false, header = true) => {
        if($(".car_area").find(".__img").length == 0){
            self.loadImg(code);
            return true;
        }
        $.ajax({
            url : page_path + 'loadError.php',
            type : 'post',
            dataType : 'json',
            data : {
                code : code,
                sealer : sealer ? 'true' : 'false'
            },
            success : function (result) {
                if(result.code == 200){
                   self.cbLoadError(result, ".car_area", '', sealer, header);

                   result.data;
                }
                else{
                    console.log(result.message);
                }
            },
            error: function(error){
				console.log(error.responseText);
			}
        })
    }
    this.cbLoadError = (result, el = ".car_area", boss = '', sealer = false, header = true) => {
        if(result.length == 0){
            console.log('result error empty!');
            return true;
        }
        let data = result.data;
        
        for(let i in data){
            console.log(data[i]);
            if(typeof data[i].error_position == "undefined") continue;
            if(data[i].error_position == '') continue;
            $(el).find(".__img img[alt=" + data[i].error_position + "]").parent().append(
                '<span class="error_point ' + ((data[i].error_type != '1' && data[i].error_type != '2') ? 'other_type_error ' : '') + ((data[i].err_level == '2') ? 'done_lv_2' : data[i].err_level == '3' ? 'done_lv_3' : data[i].err_level == '0' ? 'pending_fix' : '') + ' ' + (sealer ? 'sealer' : '') + '" mv-error="' + data[i].error_type + '" mv-position="' + data[i].error_position + '" mv-errother="' + data[i].err_other + '" mv-x="' + data[i].error_toadoX + '" mv-y="' + data[i].error_toadoY + '" style="top: ' + data[i].error_toadoY + '%; left: ' + data[i].error_toadoX + '%;" mv-errid="' + data[i].err_id + '" mv-errnote="' + (typeof data[i].err_note != 'undefined' ? data[i].err_note : '') + '"></span>'
            );
        }
        if(self.car_folder == '' && boss == '') {
            return;
        }
        if(boss != ''){
            self.car_folder = boss.toUpperCase();
        }
        $box_img = $(el).find(".__box_img");
        
		if(header){
			$box_img.each(function(){
				$title = $(this).find('.__title_header h4');
				$val_title = $title.text();
				$new_text = self.car_folder + " - " + result.color + "<br>" + $val_title;
				$title.empty().append($new_text);
			})
		}
        if($(".showOvlAddNote").length > 0){
            $(".showOvlAddNote").show();
        }
        if(position == 'SEALER'){
            $(".submitCodeSealer").show();
        }else{
            $(".viewSealer").show();
        }
        if(result.hasSealerError && !$('.viewSealer').hasClass('sealer')){
            $(".viewSealer").addClass('btn-danger');
        }else{
            $(".viewSealer").removeClass('btn-danger');
        }
    }
    this.setOwlCarousel = () =>{
        $('.car_area').addClass('owl-carousel').owlCarousel({
            loop:false,
            margin:10,
            nav:true,
            touchDrag: false,
            mouseDrag: false,
            items: 1,
            autoHeight : true
        })
    }
    this.emptyImg = () =>{
        $parent = $('.car_area').parent();
        $('.car_area').remove();
        $parent.append("<div class='car_area'></div>");
    }
    this.emptyError = () =>{
        $('.car_area').find(".__img").each(function () {
            $(this).find('span').each(function () {
                if(!$(this).hasClass('none_choice_error')){
                    $(this).remove();
                }
            })
        })
    }
    this.loadCode = (code) =>{
        $.ajax({
            url : page_path + 'loadCode.php',
            type : 'post',
            dataType : 'json',
            data : {
                code : code
            },
            success : function (result) {
                if(result.code == 200){
                    $("#car_code").val(result.message);
                    $("#header").addClass('top');
                    $(".showHeader").show();
                    self.setIn();
                    self.loadImg(result.message,true);
                }else{
                    console.log(result);
                }
            },
            error : function (error) {
                console.log(error.responseText);
            }
        })
    }
    this.loadList = (code) =>{
        $.ajax({
            url : page_path + 'loadAll.php',
            type : 'post',
            dataType : 'json',
            data : {
                type : 'getlist',
                code : code
            },
            success : function (result) {
                if(result.code == 200){
                    window.location.assign('./?code=' + result.data[0]);
                }else{
                    console.log(result);
                    alert('Error ajax load list!');
                }
            },
            error : function (error) {
                console.log(error.responseText);
            }
        })
    }
    this.changeSelectMenuCode = (el) =>{
        let code = el.val();
        if(code == '-1'){
            return false;
        }
        window.open('./?code=' + code, '_blank');
    }
    this.loadAll = (code, location = null, exportFlag = false) => {
        $.ajax({
            url : page_path + 'loadAll.php',
            type : 'post',
            dataType : 'json',
            data : {
                type : 'loadall',
                code : code,
                code_min : code.substring(0, length_car_vin)
            },
            
            success : function (result) {
                $userType = $("meta[name=_ps]").attr('content');
                if(result.code == 200){
                    if($userType == 'check_repair'){
                        self.cbLoadAll_v2(result, location, exportFlag);
                    }else{
                        self.cbLoadAll(result, location);
                    }
                }else{
                    console.log(result);
                }
            },
            error : function (error) {
                console.log(error.responseText);
              
            }
        })
    }
    this.cbLoadAll = (result, location = null) =>{
        let errors = result.error;
        let images_lh = result.image_lh;
        let images_rh = result.image_rh;
        let images_sealer = result.image_sealer;
        let img_append = '';
        if(images_rh.length > 0 && images_lh.length > 0){
            if(location != null){
                $(".car_area_custom").append("<h2 class='text-center d-block w-100'>Khu vực " + location.toUpperCase() + "</h2>").parent().removeClass('d-none');
                $(".car_area_custom").append(self.renderImage(location.toUpperCase() == 'RH' ? images_rh : images_lh));
            } else{
                $(".car_area-rh")
                    .append("<h2 class='text-center d-block w-100'>Khu vực RH</h2>")
                    .append(self.renderImage(images_rh));
                $(".car_area-lh")
                    .append("<h2 class='text-center d-block w-100'>Khu vực LH</h2>")
                    .append(self.renderImage(images_lh));
            }
        }else{
            $(".car_area").append(self.renderImage(images_sealer));
        }
       
        for(let i in errors){
            if(typeof errors[i].error_position == "undefined") continue;
            if(errors[i].error_position == '') continue;
            $(".car_area").find(".__img img[alt=" + errors[i].error_position + "]").parent().append(
                '<span class="error_point ' + ((errors[i].error_type != '1' && errors[i].error_type != '2') ? 'other_type_error ' : '') + ((errors[i].err_level == '2') ? 'done_lv_2' : (errors[i].err_level == '3' ? 'done_lv_3' : '')) + '" mv-error="' + errors[i].error_type + '" mv-errid="' + errors[i].err_id + '" mv-errlv="' + errors[i].err_level + '" mv-errother="' + errors[i].err_other + '" mv-position="' + errors[i].error_position + '" mv-x="' + errors[i].error_toadoX + '" mv-y="' + errors[i].error_toadoY + '" style="top: ' + errors[i].error_toadoY + '%; left: ' + errors[i].error_toadoX + '%;"></span>'
            );
        }

        //self.setLocationError();

    }
    this.cbLoadAll_v2 = (result, location = null, exportFlag = false) => {
        let errors = result.error;
        let images_lh = result.image_lh;
        let images_rh = result.image_rh;
        let images_sealer = result.image_sealer;

        if(exportFlag){
            $(".area-submit").removeClass('d-none');
            $(".area-default").remove();

            $(".area-submit").find('.car_area_qc1k > .car_area')
                .append("<h2 class='text-center d-block w-100'>Khu vực RH</h2>")
                .append(self.renderImage(images_rh));
            $(".area-submit").find('.car_area_qc1k > .car_area')
                .append("<h2 class='text-center d-block w-100'>Khu vực LH</h2>")
                .append(self.renderImage(images_lh));
            $(".area-submit").find('.car_area_sealer > .car_area')
                .append("<h2 class='text-center d-block w-100'>Khu vực Sealer</h2>")
                .append(self.renderImage(images_sealer));
        }else{
            $(".car_area_custom").parent()
                .prepend('<div class="row mt-3"></div>')
                .removeClass('d-none');

            $(".car_area_custom")
                .append('<h2 class="w-100 text-center mt-4">Khu vực LH</h2>')
                .append(self.renderImage(images_lh));
            $(".car_area_custom")
                .append('<h2 class="w-100 text-center mt-4">Khu vực RH</h2>')
                .append(self.renderImage(images_rh));
        }

        for(let i in errors){
            if(typeof errors[i].error_position == "undefined") continue;
            if(errors[i].error_position == '') continue;
            $(".car_area").find(".__img img[alt=" + errors[i].error_position + "]").parent().append(
                '<span class="error_point ' + ((errors[i].error_type != '1' && errors[i].error_type != '2') ? 'other_type_error ' : '') + ((errors[i].err_level == '2') ? 'done_lv_2' : (errors[i].err_level == '3' ? 'done_lv_3' : '')) + '" mv-error="' + errors[i].error_type + '" mv-errid="' + errors[i].err_id + '" mv-errlv="' + errors[i].err_level + '" mv-errother="' + errors[i].err_other + '" mv-position="' + errors[i].error_position + '" mv-x="' + errors[i].error_toadoX + '" mv-y="' + errors[i].error_toadoY + '" style="top: ' + errors[i].error_toadoY + '%; left: ' + errors[i].error_toadoX + '%;"></span>'
            );
        }

        self.setLocationError();

    }
    this.setLocationError = () => {
        $(".container-fluid").find(".error_point").each(function(){
            let text = $(this).attr('mv-error');
            let topThis = parseFloat($(this).css('top'));
            let html = '';
            if(topThis < 34){
                html = '<span style="font-size: 15px;text-align:center;position: absolute;top: 100%;left: -px;font-weight: bold;background: #fff; width: 15px; height: 15px;">'+text+'</span>'
            } else {
                html = '<span style="font-size: 15px;text-align:center;position: absolute;bottom: 120%;left: -1px;font-weight: bold;background: #fff; width: 15px; height: 15px;">'+text+'</span>';
            }
            $(this).empty().append(html);
        })
    }
    this.renderImage = (images) =>{
        console.log(images);
        let result = '';
        for(let i in images){
            let alt = (images[i].image.split('.'));
            alt.pop();
            alt = alt[alt.length - 1].split('/');
            alt = alt[alt.length - 1];
            // comment vì chưa hiểu change_design là gì ở đây
            // else if(change_design=='REPAIR'){
            //     result += `
            //         <div class="__box_img col-lg-4 col-sm-5 col-10 mt-1 mb-1">
            //             <div class="__title_img text-center">
            //                 <h6>` +  self.translate(alt) + `</h6>
            //             </div>
            //             <div class="__img mt-1 mb-1">
            //                 <img src="` + (typeof localhost != 'undefined' && localhost ? images[i].image.substr(1) : images[i].image) + `" alt="` + alt + `" class="w-100 __box_shadow">
            //             </div>
            //         </div>
            //     `;
            // }
            //else{
                result += `
                    <div class="__box_img col-lg-3 col-sm-5 col-10 mt-1 mb-1">
                        <div class="__title_img text-center">
                            <h6>` +  self.translate(alt) + `</h6>              
                        </div>
                        <div class="__img mt-1 mb-1">
                            <img src="` + (typeof localhost != 'undefined' && localhost ? images[i].image.substr(1) : images[i].image) + `" alt="` + alt + `" class="w-100 __box_shadow">
                        </div>
                    </div>
                `;
           //}
            
        }
        
        return result;
    }
    this.convertToImgAndAjaxSave = async (exp = false) =>{

        if(!confirm('Are you sure export?\nExport not replace images.')){
            return false;
        }

        if(!exp){
            $img_qc1k = await self.cbConvertImg(".car_area");
            self.ajaxExportExcel([$img_qc1k]);
            return true;
        }

        $img_qc1k = await self.cbConvertImg(".qc1k_area");
        $img_sealer = await self.cbConvertImg(".sealer_area");
        // console.log([$img_qc1k, $img_sealer]);
        // return;
        
        self.ajaxExportExcel([$img_sealer, $img_qc1k]);

        return true;
    }
    this.cbConvertImg = (el, exportPage = false, exportAuto = false) => {
        return new Promise(resolve => {
            let img = {};
            $_img = $(el).find(".__img");
            $_parent = $(el).find('.__box_img');
            $_parent.each(function(){
                let cls = '__box_img exporting col-6';
                if(exportPage && !exportAuto){
                    cls = '__box_img exporting col-12';
                }
                $(this).removeAttr('class').attr('class', cls);
            });

            setTimeout(function () {
                $_img.each(function(){
                    var nameImg = $(this).find('img').attr('alt');
                    html2canvas($(this).get(0)).then(function(canvas) {
                        img[nameImg] = canvas.toDataURL("image/png");
                    });
                });
            },100);

            let checkDoneEach = setInterval(function(){
                if(Object.size(img) == $_img.length){
                    clearInterval(checkDoneEach);
                    resolve(img);
                }
            },50);
        });
    }
    this.ajaxExportExcel = (img) => {
        $.ajax({
            url : page_path + 'exportExcel.php',
            type : 'POST',
            data: {
                'images' : img,
                'vin_code' : $("#car_code").val(),
                'vin_code_mini' : $("#car_code").val().substring(0, length_car_vin)
            },
            dataType: 'json',
            beforeSend : function(){
                $("body").append('<div class="__loading"><div class="lds-dual-ring"></div></div>');
            },
            success: function(result){
                if(result.code == '200'){
                    // window.open(window.location.href + _prePath + result.url, '_blank');
                    window.location.href = "./?name=" + result.url;
                }
            },
            error : function(error){
                $(".__loading").append('<span>An error hased! please check console or contact admin!</span>');
                console.log(error.responseText);
            }
        }).done(function () {
            $("body").find(".__loading").remove();
        })
    }
    this.setExport = (el) =>{
        let href = el.data('href') + "&code=" + $("#car_code").val();
        window.location.href = href;
    }
    this.setIn = () =>{
        $(".setExport").removeClass('d-none');
        $(".setExportOut").addClass('d-none');
        $("#header").addClass('top');
        let checkFindedCarousel = setInterval(function () {
            if($("#bodyer").find('.owl-nav').length > 0){
                $("#bodyer").find('.owl-nav').addClass('top');
                clearInterval(checkFindedCarousel);
            }
        },50);
        $("#vincode_select_user").hide();
        $(".showHeader").show();
    }
    this.setOut = () =>{
        $(".setExport").addClass('d-none')
        $(".setExportOut").removeClass('d-none')
        $("#header").removeClass('top');
        $(".owl-nav").removeClass('top');
        $(".showHeader").hide();
        // $("#vincode_select_user").();
    }
    this.doneError = (el = null, array = null, sealer = false) => {
        let err_id = el != null ? el.attr('mv-errid') : '0';
        let level = $("meta[name=_level]").attr('content');
        let arrError = array != null ? array : [];
        let noteError = $('#modal_error').find('#note_this_error').length > 0 ? $('#modal_error').find('#note_this_error').val().trim() : '';
        let err_code = $("#car_code").length > 0 ? $("#car_code").val() : $("#vin-car-change").val();
        let polish = '';
		let usercode = '';
		if(arrError.length > 0){
			usercode = arrError[0].usercode;
		}
		if(typeof noteError == 'undefined' || noteError == undefined){
            noteError = '';
        }
        if(array != null){
            polish = $("meta[name=_polish]").attr('content');
        }
        $.ajax({
            url : page_path + 'changeLevelError.php',
            type : 'POST',
            dataType : 'json',
            data : {
                id : err_id,
                err_code : err_code,
                level : level,
                arrError : arrError,
                sealer : sealer ? 'true' : 'false',
                noteError : noteError,
                polish : polish,
                usercode : usercode
            },
            success : function (result) {
				console.log(result);
                if(result.type == 'error'){
                    console.log(result.res);
                }else{
                    if(array == null){
                        if(result.res.code == 200){
                            $(".car_area").find('.error_point[mv-errid='+ err_id +']').addClass(level == '2' ? "done_lv_2" : "done_lv_3").attr('mv-errlv', level).attr('mv-errnote', result.res.note);
                        }
                    }else{                        
                        for(let i in arrError){
                            $(".car_area").find('.error_point[mv-errid='+ arrError[i]['err_id'] +']').addClass(level == '2' ? "done_lv_2" : "done_lv_3").attr('mv-errlv', level);
                        }
                        $(".car_area").find(".error_point.__no_check").removeClass('__no_check');
                    }
                    // if($('meta[name=_ps]').attr('content') == 'check_repair'){
                    //     self.checkDoneAll().then(r => {
                    //         console.log(r)
                    //     });
                    // }
                }
            },
            error : function (error) {
                console.log(error.responseText);
            },
            complete : function () {
                $("#modal_error").modal('hide');
                $(".__overlay").find('.cancelOverlayButton').click();
            }
        })
    }
    this.checkDoneAll = async (isNotCheck = false, exportAuto = false) => {
        if(!isNotCheck && $(".car_area").find(".error_point:not(.__no_check):not(.done_lv_3)").length > 0){
            return true;
        }
        //show overlay waiting load sealer
        $("body").append('<div class="__loading"><div class="lds-dual-ring"></div></div>');
        //load sealer
        await self.loadAll($("#car_code").val().trim(), null, true);
        var pendingLoadImageAll = setInterval(() => {
            if($(".area-submit").find('.__box_img').length > 0){
                clearInterval(pendingLoadImageAll);
                convertImg();
            }
        }, 100)
        async function convertImg() {
            $img_qc1k = await self.cbConvertImg(".qc1k_area", isNotCheck, exportAuto);
            $img_sealer = await self.cbConvertImg(".sealer_area", isNotCheck, exportAuto);
            self.saveImgIfDoneAll({
                qc1k : $img_qc1k,
                sealer : $img_sealer
            });
        }
        return true;
    }
    this.saveImgIfDoneAll = (img) => {
        var exportByTool = false;
        if(typeof allowExport != 'undefined'){
            if(allowExport){
                exportByTool = true;
            }
        }
        $.ajax({
            url : page_path + 'saveImageDoneAll.php',
            type : 'POST',
            data: {
                'images' : img,
                'vin_code' : $("#car_code").val(),
                'vin_code_mini' : $("#car_code").val().substring(0, length_car_vin),
                'tool' : exportByTool ? '1' : '0'
            },
            dataType: 'json',
            success: function(result){
                console.log(result);
                if(exportByTool){
                    window.location.href = './?all=1';
                    return true;
                }
                window.location.href = window.location.origin + window.location.pathname;
                return true;
            },
            error : function(error){
                console.log(error.responseText)
                alert('An error hased! please check console or contact admin!');
                if(exportByTool){
                    window.location.reload(true);
                    return true;
                }
            }
        })
    }
    this.inArray = (item, array) => {
        for(let i in array){
            if(item == array[i]) return true;
        }
        return false;
    }
    this.polishSubmit = (location = null, usercode = null, el = null) =>{
        $userType = $("meta[name=_ps]").attr('content');
        if(!self.inArray($userType, ['check_repair', 'polishing'])){
            if(usercode == null){
                $("meta[name=_polish]").attr('content', el.attr('mv-polish'))
                $(".__overlay").show()
                    .attr('mv-el', el.attr('mv-class'))
                    .attr('mv-type', 'submit');
                return;
            }
            if(usercode == ''){
                alert('Hãy nhập mã nhân viên trước khi submit!');
                return;
            }
        }
        if(!confirm('Submit this car?')){
            return false;
        }
        let _node = location == null ? $(".car_area") : location;
        let errFind = null;
        if($userType == 'check_repair'){
            errFind = _node.find(".error_point:not(.__no_check):not(.done_lv_3)");
        }else{
            errFind = _node.find(".error_point:not(.__no_check):not(.done_lv_2):not(.done_lv_3)");
        }
        let errError = [];
        errFind.each(function(){
            let temp = {
                err_id : $(this).attr('mv-errid'),
                level : $("meta[name=_level]").attr('content'),
                usercode : usercode
            }
            errError.push(temp);
        })
        self.doneError(null,errError);
    }
    this.modalNoteButtonSave = (el) => {
        var modal = $(el).closest('.modal');
        var note = modal.find("#note_car").val().trim();
        self.saveNote(note);
        modal.modal('hide');
    }
    this.saveNote = (note_car) => {
        $car_code = 'Error get car note';
        if($("#car_code").length > 0){
            $car_code = $("#car_code").val();
        }else if($("#vin-car-change").length > 0){
            $car_code = $("#vin-car-change").val();
        }

        $.ajax({
            url: page_path + 'addNote.php',
            type : 'POST',
            dataType : 'json',
            data: {
                car_code : $car_code,
                note_car : note_car,
                loadNote : '0'
            },
            success : (result) => {
                console.log(result);
            },
            error : (error) => {
                console.log(error.responseText);
            }
        });
    }
    this.loadNoteSingle = (el, code) => {
        if($(el).length == 0){
            return false;
        }

        $.ajax({
            url: page_path + 'addNote.php',
            type : 'POST',
            dataType : 'json',
            data: {
                car_code : code,
                note_car : '',
                loadNote : '1'
            },
            success : (result) => {
                if(result.code == 200){
                    $(el).val(result.note)
                }else{
                    console.log(result)
                }
            },
            error : (error) => {
                console.log(error.responseText);
            }
        });
    }
    this.viewHistory = (el,id) =>{
        $user = el.attr('mv-user') != undefined ? el.attr('mv-user') : '';
        $.ajax({
            url : page_path + 'history.php',
            type : 'post',
            dataType : 'json',
            data : {
                err_id : id,
                user : $user
            },
            success : function (result) {
                self.cbViewHistory(el,result);
            },
            error : function (error) {
                console.log(error.responseText)
            }
        })
    }
    this.cbViewHistory = (el,result) =>{
        console.log(result)
        let data = result.data;
  
        let html = `
            <table class="table table-responsive">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Error Name</th>
                        <th scope="col">User Change</th>
                        <th scope="col">Time Change</th>
                        <th scope="col">Date Change</th>
                    </tr>
                </thead>
                <tbody>
            `;
        let ii = 1
        for(let i in data){
            let userChange = data[i]['err_user_fullname'] != null ? data[i]['err_user_fullname'] : data[i]['err_user_change'];
            html += `
                    <tr>
                        <th scope="row">` + (ii++) + `</th>
                        <td>`+err_option_qc1k[result.type.error_type]+`</td>
                        <td>`+userChange+`</td>
                        <td>`+data[i]['err_time_change']+`</td>
                        <td>`+data[i]['err_date_change']+`</td>
                    </tr>
            `;
        }
        html +=`
                </tbody>
            </table>
        `;

        el.modal("show").find(".modal-body").empty().append(html);
    }
    this.translate = (message) =>{
        if(typeof __translate == 'undefined' || typeof __translate[message] == 'undefined'){
            return message;
        }
        return __translate[message];
    }
    this.submitCodeSealer = () =>{
        if(!confirm("Are you sure submit this car?")){
            return false;
        }

        let val = $("#car_code").length > 0 ? $("#car_code").val() : $("#vin-car-change").val();
        
        $.ajax({
            url : page_path + 'submitCodeSealer.php',
            type : 'post',
            dataType : 'json',
            data : {
                code : val
            },
            success : function (result) {
                if(result.type == '1'){
                    alert('Add done');
                    self.submitErrorSealer();
                    window.location.reload();
                }else if( result.type == '0'){
                    alert('Car code added to ' + result.time);
                    self.submitErrorSealer();
                    window.location.reload();
                }
                if(result.code != '200'){
                    alert(result.data);
                }
            },
            error : function (error) {
                console.log(error.responseText)
            }
        })
    }
    this.submitReFix = (el) => {
        $id = el.attr('mv-errid');
        console.log($id);
        $modal = $("#modal_error");
        $note = $modal.find('#note_this_error').val().trim();
        $.ajax({
            url : page_path + "submitSealerError.php",
            type: 'POST',
            dataType : 'json',
            data : {
                errid : $id,
                note : $note
            },
            success : function (result) {
                if(result.code == 200){
                    $("#bodyer").find('.error_point[mv-errid=' + $id + ']').addClass('pending_fix')
                }else{
                    alert('Have an error. Please contact to ADMIN!')
                }
            },
            error : function (error) {
                console.log(error.responseText)
            },
            complete : function () {
                $modal.modal('hide');
            }
        });
    }
    this.submitErrorSealer = () => {
        $code = $("#vin-car-change").val();
        $.ajax({
            url : page_path + "submitSealerError.php",
            type: 'POST',
            dataType : 'json',
            data : {
                code : $code,
                all : 'true'
            },
            success : function (result) {
                console.log(result);
                if(result.code == 200){
                    $(".car_area").find('.error_point.done_lv_2').each(function () {
                        $(this).removeClass('done_lv_2').addClass('done_lv_3')
                    })
                }else{
                    alert(result.message)
                }
            },
            error : function (error) {
                console.log(error.responseText)
            }
        });
    }
    this.recoatCar = (usercode = null) => {
        if(usercode == null){
            if(!confirm('Xác nhận sơn lại xe này?')){
                return true;
            }
            $(".__overlay").show().attr('mv-type', 'recoat');
            return;
        }
        if(usercode == ''){
            alert('Hãy nhập mã nhân viên trước khi submit!');
            return;
        }
        $code = $("#vin-car-change").length > 0 ? $("#vin-car-change").val() : $("#car_code").val();
        $.ajax({
            url : page_path + "recoatCar.php",
            type: 'POST',
            dataType : 'json',
            data : {
                code : $code,
                usercode : usercode
            },
            success : function (result) {
                if(result.code == 200){
                    window.location.assign('./');
                }else{
                    alert(result.message)
                }
            },
            error : function (error) {
                console.log(error.responseText)
            }
        });
        return true;
    }
    this.getDownloadFileExport = () => {
        $code = $("#vin-car-change").length > 0 ? $("#vin-car-change").val() : $("#car_code").val();
        $.ajax({
            url : page_path + "getFileToDownloadExport.php",
            type: 'POST',
            dataType : 'json',
            data : {
                code : $code
            },
            success : function (result) {
                console.log(result)
            },
            error : function (error) {
                console.log(error.responseText)
            }
        });
    }
    this.showOvlAddNote = () => {
        $("#modal_note").modal('show');
        $("#header").addClass('top')
    }
    this.fullScreen = () => {
        // if($("#_body").length == 0){
        //     $(this).attr('id', '_body');
        // }
        // var elem = document.getElementById("_body");
        // if (elem.requestFullscreen) {
        //     elem.requestFullscreen();
        // } else if (elem.mozRequestFullScreen) { /* Firefox */
        //     elem.mozRequestFullScreen();
        // } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
        //     elem.webkitRequestFullscreen();
        // } else if (elem.msRequestFullscreen) { /* IE/Edge */
        //     elem.msRequestFullscreen();
        // }
    }
}
var car = new Car();