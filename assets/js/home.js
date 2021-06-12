$(document).ready(function () {
    //block drag img
    
    $('img').on('dragstart', function(event) { event.preventDefault(); });

    //check isset code
    let begin_code = $("#car_code").length > 0 ? $("#car_code").val().trim() : 0;
    if(begin_code.length == length_car_vin){
        car.loadImg(begin_code);
    }
    else if(begin_code.length == length_car_code){
        car.loadImg(begin_code,true);
        car.setIn();
    }
    else {
        $("#car_code").val('')
    }

    //biến global
    var _modal = $("#modal_error");
    var _old_modal = [];

    $("#bodyer")
        .on('click', '.__img img', function (e) {
            if(noImportError || isCreateError == '0'){
                return true;
            }
            let _this = $(this);
            let _parent = _this.parent();

            let _w = _this.width();
            let _h = _this.height();

            let _x = ((e.pageX - _this.offset().left) / (_w) * 100).toFixed(2);
            let _y = ((e.pageY - _this.offset().top) / (_h) * 100).toFixed(2);

            let _position = _this.attr('alt');

            _parent.append(
                '<span class="error_point none_choice_error" mv-position="' + _position + '" mv-x="' + _x + '" mv-y="' + _y + '" style="top: ' + _y + '%; left: ' + _x + '%;"></span>'
            );

            // _modal.modal('show');

        })
        .on('click', '.__img .error_point', function (event) {
            let _this = $(this);
            car.this_error = _this;
            let error_type = _this.attr('mv-error') !== undefined ? _this.attr('mv-error') : '';

            let err_other = _this.attr('mv-errother') != undefined ? _this.attr('mv-errother') : '';

            _old_modal = {
                mv_type : _modal.find('.submit').attr('mv-type'),
                title : _modal.find('#modal_error_title').text()
            };

            let _x = $(this).attr('mv-x');
            let _y = $(this).attr('mv-y');

            if(!_this.hasClass('sealer')){
                _modal.find('.delete').removeClass('d-none');
                _modal.find('.delete')
                    .attr('mv-x',_x)
                    .attr('mv-y', _y)
                    .attr('mv-type', 'delete');
            }else if(_this.hasClass('sealer')){
                _modal.find('#error_type').attr('disabled', 'true');
                if(
                    (!_this.hasClass('done_lv_2') || (_this.hasClass('done_lv_2') && isCreateError == '1'))
                    && !_this.hasClass('done_lv_3')
                ){
                    _modal.find('.checked_btn').show().attr('mv-errid',_this.attr('mv-errid'));
                    _modal.find('.submit').hide();
                }
            }

            _modal.find('#modal_error_title').text('Sửa lỗi');
            _modal.find('select').val(error_type);

            if(error_type == err_option_other_id) {
                _modal.find('.error-other-group').show().find("#err_other").val(err_other);
            }

            if(!$(this).hasClass('none_choice_error')){
                _modal.find('.submit')
                    .attr('mv-x',_x)
                    .attr('mv-y', _y)
                    .attr('mv-type', 'edit');
            }else{
                _modal.find('#err_other').removeAttr('readonly');
            }
            if($(this).hasClass('done_lv_2') || $(this).hasClass('done_lv_3')){
                _modal.find(".submit").hide();
                _modal.find('#error_type').attr('disabled','');
            }
            if(!_this.hasClass('none_choice_error')){
                if(_this.hasClass('done_lv_2')){
                    if(!_this.hasClass('done_lv_3')){
                        _modal.find('.note-group').show();
                        _modal.find('.checked_btn').show();
                    }
                    _modal.find('.submit').hide();
                    _modal.find('.delete').addClass('d-none');
                }
				if((_this.hasClass('done_lv_2') &&  _this.hasClass('sealer')) || (_this.hasClass('done_lv_3') &&  _this.hasClass('sealer'))){
                    let notes = _this.attr('mv-errnote');
                    if(typeof notes != 'undefined' && notes != 'null' && notes != undefined && notes != '')
						_modal.find('.note-group').show().find('#note_this_error').val(notes).attr('disabled', 'true');
                }
            }
            _modal.modal('show');
        });
    _modal.on('hide.bs.modal',function () {
        _modal.find('.submit')
            .removeAttr('mv-x')
            .removeAttr('mv-y')
            .attr('mv-type', _old_modal.mv_type);
        _modal.find('.delete')
            .removeAttr('mv-x')
            .removeAttr('mv-y')
            .removeAttr('mv-type');
        _modal.find('#modal_error_title').text(_old_modal.title);
        _modal.find('#error_type').val('-1');
        _modal.find('.delete').addClass('d-none');
        _modal.find(".submit").show();
        _modal.find('#error_type').removeAttr('disabled');
        _modal.find('.error-other-group').hide().find("#err_other").val('').attr('readonly','true');
        if(_modal.find('.checked_btn').length > 0){
            _modal.find('.checked_btn').hide();
        }
        _modal.find('.note-group').hide().find('#note_this_error').val('').removeAttr('disabled');
        return true;
    })

    $("#car_code").on('keyup',function (event) {
        let val = $(this).val().trim();
        if(position == 'ADMIN'){
            if(val.length == length_car_code - length_car_vin){
                car.loadList(val);
            }
            return true;
        }
        if(val.length == length_car_code - length_car_vin){
            car.loadCode(val);
        }
        else if(val.length < length_car_vin){
            car.emptyImg();
        }

        else if(val.length == length_car_vin){
            car.loadImg(val);
        }
        else if(val.length == length_car_code){
            car.loadError(val);
            car.setIn();
        }
        else if((val.length > length_car_vin && val.length < length_car_code) || val.length > length_car_code){
            car.emptyError();
            car.setOut();
        }
    })
});