<?php
    if(!isset($_SESSION)) session_start();
    if(isset($home_folder)){
        $url = $home_folder;
    }
    else{
        if(strpos($_SERVER['HTTP_HOST'], 'localhost') == 0){
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/';
        }else{
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
        }
    }

    $aryHideHeader = ['polishing', 'sealer'];
    $aryShowNote = ['polishing', 'repair', 'check_repair'];
//    $aryShowNoteDisabled = ['check_repair'];

?>

<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php if(isset($_SESSION['logined']['user_check'])) : ?>
        <meta name="_ps" content="<?=$_SESSION['logined']['user_check']?>">
        <meta name="_level" content="<?=($_SESSION['logined']['user_check'] == 'check_repair' ? '3' : '2')?>">
        <meta name="_type" content="<?=($_SESSION['logined']['user_check'] == 'repair' ? '2' : '1')?>">
    <?php endif; ?>
    <title>//</title>
    <meta name="_polish" content="">
    <link rel="shortcut icon" href="<?=$url . 'assets/images/favicon.png'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/bootstrap/css/bootstrap.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/owl_carousel/css/owl.carousel.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/owl_carousel/css/owl.theme.default.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/FontAwesome_4.7/css/font-awesome.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'assets/css/home.css'?>">
    <script src="<?=$url . 'vendor/jquery/jquery.min.js'?>"></script>
    <style>
        .error_point{
            width: 15px;
            height: 15px;
        }
        #history_modal .modal-body tr th, #history_modal .modal-body tr td{
            text-align: center;
        }
        #header{
            transition: all .2s;
        }
        <?php if(in_array($_SESSION['logined']['user_check'], $aryHideHeader)) :  ?>
            .__hard_menu{
                position: fixed;
                top: 0;
                right: 0;
                padding: 7px 22px;
                background: darkcyan;
                color: #fff;
                border-radius: 3px;
                cursor: pointer;
                transition: all .2s;
                z-index: 1366;
            }
        <?php endif; ?>
        @media (min-width: 992px){
            #history_modal .modal-dialog {
                max-width: 600px;
                margin: 1.75rem auto;
            }
        }
    </style>
    <?php
        $aryViewListError = ['POLISHING', 'REPAIR', 'CHECK_REPAIR'];
        $userCheck = $_SESSION['logined']['user_check'] ?? '';
        $viewListError = !!in_array(strtoupper($userCheck),$aryViewListError);
    ?>
    <?php
        if(isset($_GET['all']) && $_GET['all'] == '1'){
            $allowExport = true;
            echo "<script>var allowExport = true;</script>";
        }
    ?>
</head>
<body>

<?php if(in_array($_SESSION['logined']['user_check'], $aryHideHeader)) :  ?>
<!--<div class="__hard_menu" onclick="$('#header').toggleClass('top');">-->
<!--    <i class="fa fa-bars"></i>-->
<!--</div>-->
<?php endif; ?>

<div class="__overlay" style="display: none">
    <div class="__area">
        <h3>Nhập mã số nhân viên</h3>
        <input type="text" class="form-control" id="__usercode">
        <button class="btn btn-primary mt-2 submitOverlayButton">Submit</button>
        <button class="btn btn-danger mt-2 cancelOverlayButton" onclick="$('.__overlay').hide(); $('#__usercode').val('')">Cancel</button>
    </div>
</div>

<?php if($viewListError) : ?>
    <div id="_bg_errorConfig" class="d-none" onclick="$('.showErrorConfig').click()"></div>
    <div id="errorConfig" class="left">
        <div class="_close" onclick="$('.showErrorConfig').click()">
            <i class="fa fa-close"></i>
        </div>
        <div class="_list"><ul></ul></div>
    </div>
<?php endif; ?>

<section id="header">
    <?php if($viewListError) : ?>
        <div class="showErrorConfig" onclick="$('#errorConfig').toggleClass('left');$('#_bg_errorConfig').toggleClass('d-none');$('body').toggleClass('no-scroll')">
            <i class="fa fa-bars"></i>
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row pt-3 pb-3">
            <div class="col-8 d-flex">
                <input type="text" class="form-control d-inline-block w-100" id="car_code" placeholder="Car code" value="<?=$code?>" disabled>
<!--                <button class="btn btn-success w-25 d-inline-block ml-3 button_header" onclick="return false;" disabled>Export</button>-->
            </div>
            <div class="col-4">
                <div class="row_1">
                    <h4 class="d-inline-block">Hi <a href="./"><?=$_SESSION['logined']['username']?></a>!</h4>
                    <a href="<?=$url . 'login/logout.php'?>" class="pl-1 d-inline-block">Logout</a>
                </div>
                <div class="row_2 showFullName">
                    <?=$_SESSION['logined']['fullname'] ?? ''?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if(in_array($_SESSION['logined']['user_check'], $aryHideHeader)) : ?>
    
    <section class="choiceCar">
        <div class="button-group text-center mt-5">
            <button class="btn btn-primary ml-3 mr-3 pl-4 pr-4 buttonChoice" data-choice="RH">RH</button>
            <button class="btn btn-primary ml-3 mr-3 pl-4 pr-4 buttonChoice" data-choice="LH">LH</button>
        </div>
    </section>
<?php endif; ?>

<section id="bodyer" class="mt-2">
    <div class="container-fluid area-default">
        <div class="row justify-content-center">
            <div class="col-12 d-none">
                <div class="row car_area car_area_custom align-items-start" style="border-right: 1px solid #ddd"></div>
            </div>
            <div class="col-6">
                <div class="row car_area car_area-lh align-items-start pt-2 pb-2" style="border-right: 1px solid #ddd; overflow: auto;position: relative"></div>
            </div>
            <div class="col-6">
                <div class="row car_area car_area-rh align-items-start pt-2 pb-2" style="border-left: 1px solid #ddd; overflow: auto;position: relative"></div>
            </div>
        </div>
    </div>
    <div class="container-fluid area-submit d-none">
        <div class="row justify-content-center">
            <div class="col-12 car_area_qc1k qc1k_area">
                <div class="row car_area align-items-start" style="border-right: 1px solid #ddd"></div>
            </div>
            <div class="col-12 car_area_sealer sealer_area">
                <div class="row car_area align-items-start" style="border-right: 1px solid #ddd"></div>
            </div>
        </div>
    </div>
</section>

<!--  Modal  -->
<div class="modal fade" id="modal_error" tabindex="-1" role="dialog" aria-labelledby="modal_error" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_error_title">Xem chi tiết</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <h6>Loại lỗi</h6>
                </div>
                <div class="form-group">
                    <select name="error_type" id="error_type" class="form-control" disabled></select>
                </div>
                <div class="form-group error-other-group" style="display: none">
                    <label for="err_other">Lỗi cụ thể</label>
                    <textarea name="err_other" id="err_other" class="form-control" rows="3" readonly onfocus="$(this).removeAttr('style')"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <?php if(isset($_SESSION['logined']['user_check'])) : ?>
                    <button class="btn btn-success checked_btn" onclick="car.doneError($(this))">Checked</button>
                    <button class="btn btn-primary view_history_btn" style="display: none">History</button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_SESSION['logined']['user_check']) && $_SESSION['logined']['user_check'] == 'check_repair') : ?>
    <!-- Modal History-->
    <div class="modal fade" id="history_modal" tabindex="-1" role="dialog" aria-labelledby="history_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['logined']['user_check']) && in_array(strtolower($_SESSION['logined']['user_check']), $aryShowNote)) : ?>
    <style>
        .addNote textarea, .addNote .select-group{
            overflow:hidden;
            padding:10px;
            max-width:767px;
            width: 100%;
            font-size:16px;
            margin:50px auto;
            display:block;
            border-radius:10px;
            border:1px solid #556677;
            outline: none;
            resize: none;
            font-weight: 600;
            height: auto;
            position: relative;
        }
        .addNote textarea:disabled{
            background: #f9f9f9;
        }
        @media (max-width: 767px){
            .addNote textarea, .addNote .select-group{
                margin:50px 10px;
            }
        }
    </style>
    <?php
        require_once "./vendor/PDOconnect/pdo/PdoConnect.php";
        $db = new pdoRequest();
        $db->setTable('note_car');
        $result = $db->one(['vin_code', $code]);
    ?>
    <div class="addNote addNoteController" style="display: none">
       
            <textarea rows='3' class="note_car" id="note_car" data-autoresize placeholder='Write Note For This Car'><?=$result['note'] ?? ''?></textarea>


        <?php if(strtolower($_SESSION['logined']['user_check']) == 'polishing') : ?>
            <div class="select-group"></div>
        <?php endif; ?>
    </div>
    <!-- <script>
        function resizeTextarea(el) {
            var offset = el.offsetHeight - el.clientHeight;
            jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
        }
        jQuery.each(jQuery('textarea[data-autoresize]'), function() {
            resizeTextarea(this);
            var that = this;
            setTimeout(function(){
                $(that).trigger('keyup')
            },500)
            jQuery(this).on('keyup input focus', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
        });
    </script> -->
<?php endif; ?>

<?php if(in_array(strtolower($_SESSION['logined']['user_check']), $aryShowNote)) : ?>
    <section id="footer" class="mb-5 mt-3">
        <div class="text-center">
            <?php if(isset($_SESSION['logined']['user_check']) && $_SESSION['logined']['user_check'] == 'polishing') : ?>
                <button class="btn btn-outline-danger addNoteController" style="display:none;" id="edit_note" mv-type="1" disabled onclick="car.saveNote($('.note_car').val());">Save Note</button>
                <button class="btn btn-primary addNoteController" style ="display: none" onclick="car.polishSubmit()">Submit</button>
            <?php elseif (in_array(strtolower($_SESSION['logined']['user_check']), ['repair', 'check_repair'])) :  ?>
                <button class="btn btn-outline-danger addNoteController" id="edit_note" mv-type="1" onclick="car.saveNote($('.note_car').val());">Save Note</button>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<div class="_go_home" onclick="$('#header').toggleClass('top')">
    <i class="fa fa-home"></i>
</div>

<script src="<?=$url . 'vendor/bootstrap/js/popper.min.js'?>"></script>
<script src="<?=$url . 'vendor/bootstrap/js/bootstrap.min.js'?>"></script>
<script src="<?=$url . 'vendor/html2canvas/html2canvas.min.js'?>"></script>
<script src="<?=$url . 'vendor/owl_carousel/js/owl.carousel.js'?>"></script>
<script src="<?=$url . 'assets/js/translate.js'?>"></script>
<script src="<?=$url . 'assets/js/err_json.js'?>"></script>
<script src="<?=$url . 'assets/js/car.js'?>"></script>
<script>
    var flagReload = '0';
    var autoload = false;
</script>
<?php if(isset($_SESSION['logined']['user_check']) && in_array(strtolower($_SESSION['logined']['user_check']), $aryShowNote)) : ?>
    <script src="<?=$url . 'assets/js/autoCompleteNote.js'?>"></script>
    <script>
        $(document).ready(function(){
            Object.size = function(obj) {
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            };

            flagReload = localStorage.getItem('__flag_export') || '0';
            if(flagReload == '1'){
                localStorage.removeItem('__flag_export');
                autoload = true;
                car.checkDoneAll(true, true).then(r => {
                    console.log(r)
                });
                return true;
            }

            function removeItemToArray(arr) {
                var what, a = arguments, L = a.length, ax;
                while (L > 1 && arr.length) {
                    what = a[--L];
                    while ((ax= arr.indexOf(what)) !== -1) {
                        arr.splice(ax, 1);
                    }
                }
                return arr;
            }
            let select_note = '';
            for(let i = 1; i <= Object.size(note_complete); i++){
                select_note += `
                    <div class="form-group m-0">
                        <input type="checkbox" class="checkboxNote" id="checkbox` + i + `"  data-faq="` + note_complete[i] + `">
                        <label class="m-0" for="checkbox` + i + `">` + note_complete[i] + `</label>
                    </div>
                `;
            }
            $(".select-group").append(select_note).on('change','.checkboxNote', function () {
                let text = $(".note_car").val();
                if(!$(this).prop('checked')){
                    if(selectCheckboxText.length != 0){
                        removeItemToArray(selectCheckboxText, $(this).data('faq'));
                        text = text.replace($(this).data('faq'), '');
                    }
                } else{
                    selectCheckboxText.push($(this).data('faq'))
                    text = text + '\r\n' + $(this).data('faq');
                }
                $(".note_car").val(text);
                $("#edit_note").removeAttr('disabled');
                if(typeof resizeTextarea != 'undefined' && document.getElementById('note_car') != null){
                    resizeTextarea(document.getElementById('note_car'));
                }
            });

        })
    </script>
<?php endif; ?>
<script>
    var length_car_vin = 9;
    var length_car_code = 17;
    var position = '<?=$_SESSION['logined']['position']?>';
    var location_user = '<?=$_SESSION['logined']['location'] ?? '' ?>';
    var showChecked = false;
    var selectCheckboxText = [];
    var err_option = position == 'SEALER' ? err_option_sealer : err_option_qc1k;
    var err_option_other_id = position == 'SEALER' ? err_option_sealer_other_id : err_option_qc1k_other_id;
    var change_design = '<?=$_SESSION['logined']['username']?>';

    $(document).ready(function(){

   
        if(flagReload == '1'){
            return true;
        }
        function renderErrorList(){
            let html = '';
            var keys = Object.keys(err_option);
            for(let i = 0; i < keys.length; i++){
                html += "<li>" + keys[i] + " : " + err_option[keys[i]] + "</li>";
            }
            $("#errorConfig ._list ul").empty().append(html);
        }
        <?php if($viewListError) : ?>
            renderErrorList();
        <?php endif; ?>
        if(typeof resizeTextarea != 'undefined' && document.getElementById('note_car') != null){
            resizeTextarea(document.getElementById('note_car'));
        }
        <?php if(in_array($_SESSION['logined']['user_check'], $aryHideHeader)) :  ?>
            $(".buttonChoice").click(function () {
                console.log('adsd');
                let location = $(this).data('choice');
                car.loadAll($("#car_code").val().trim(), location);
                $(this).parent().hide();
                $(".addNoteController").show();
                if(typeof resizeTextarea != 'undefined' && document.getElementById('note_car') != null){
                    resizeTextarea(document.getElementById('note_car'));
                }
                $("#header").addClass("top");
                $(".button_header").removeAttr('disabled');
            })
            if(location_user != ''){
                console.log($(".buttonChoice[data-choice=" + location_user.toUpperCase() + "]"));
                $(".buttonChoice[data-choice=" + location_user.toUpperCase() + "]").click();
            }
        <?php endif; ?>
        if($("#car_code").length > 0){
            let val = $("#car_code").val().trim();
            <?php if(!in_array($_SESSION['logined']['user_check'], $aryHideHeader)) :  ?>
                $("#header").addClass("top");
                car.loadAll(val);
                $(".addNoteController").show();
            <?php endif; ?>
            // $(".addNoteController").show();
            // car.loadAll(val);
        }
        <?php if(in_array($_SESSION['logined']['user_check'], ['repair'])) :  ?>
            $content = `
                <button class="btn btn-info _submit" style="position: absolute;top: 0;z-index: 12; right: 0;">Submit</button>
            
                <button class="btn btn-outline-danger _recoat" style="position: absolute;top: 0;z-index: 12; left: 0;" onclick="car.recoatCar()">Recoat</button>
            `;
          
            $(".car_area-lh").append($content)
                .find('._submit').attr('onclick', 'car.polishSubmit($(\'.car_area-lh\'),null,$(this))').attr('mv-class','.car_area-lh').attr('mv-polish', 'LH');
            $(".car_area-rh").append($content)
                .find('._submit').attr('onclick', 'car.polishSubmit($(\'.car_area-rh\'),null,$(this))').attr('mv-class', '.car_area-rh').attr('mv-polish', 'RH');
        <?php endif; ?>
        <?php if(in_array($_SESSION['logined']['user_check'], ['check_repair'])) :  ?>
            $content = `
                <button class="btn btn-info _submit" style="position: fixed !important;top: 0;z-index: 12; right: 0;">Submit</button>
                <button class="btn btn-outline-danger _recoat" style="position: absolute;top: 0;z-index: 12; left: 0;" onclick="car.recoatCar()">Recoat</button>
            `;

           
            $(".car_area_custom").append($content)
                .find('._submit')
                .attr('onclick', 'car.polishSubmit($(\'.car_area_custom\'),null,$(this))')
                .attr('mv-class','.car_area_custom');

        <?php endif; ?>
        var _modal = $("#modal_error");
        $(".view_history_btn").click(function () {
            let id = $(this).attr('mv-errid');
            car.viewHistory($("#history_modal"),id);
        })
        $(".car_area").on('click', '.error_point', function () {
            // thay doi trang thai clcik loi
            <?php if($_SESSION['logined']['user_check'] == 'repair') : ?>
                if($(this).hasClass('__no_check')){
                    $(this).removeClass('__no_check');
                }else{
                    $(this).addClass('__no_check');
                }
                return true;
            <?php endif; ?>
            let _type = $(this).attr('mv-error');
            if(
                $("meta[name=_type]").attr('content') == "1"
                 && $(this).attr('mv-errlv') != "3"
            ){
                $(this).toggleClass('__no_check');
                checkPolish = true;
                return;
            }
            _modal.modal('show')
                .find('#error_type').empty()
                .append('<option value=""> ' + err_option[_type] +  ' </option>');
            if(
                _modal.find('.view_history_btn').length > 0
                && $(this).attr('mv-errlv') == "3"
                && $("meta[name=_level]").attr('content') == "3"
            ){
                _modal.find(".view_history_btn")
                    .attr('mv-errid',$(this).attr('mv-errid'))
                    .show();
            }
            if($(this).attr('mv-errlv') == "3" || $(this).attr('mv-errlv') == "2"){
                _modal.find('.checked_btn')
                    .attr('mv-errid',$(this).attr('mv-errid'))
                    .hide();
                showChecked = true;
            }
            if(_modal.find('.checked_btn').length > 0 && $(this).attr('mv-errlv') != "3"){
                _modal.find('.checked_btn')
                    .attr('mv-errid',$(this).attr('mv-errid'))
                    .show();
            }
            let err_other = $(this).attr('mv-errother') != undefined ? $(this).attr('mv-errother') : '';
            if(err_other == '' || err_other == 'null'){
                err_other = 'Chưa có bản ghi!';
            }
            _modal.find('.error-other-group').show().find('#err_other').val(err_other);
        })
        _modal.on('hide.bs.modal',function () {
            // if(_modal.find('.checked_btn').length > 0){
            //     _modal.find('.checked_btn')
            //         .removeAttr('mv-errid')
            //         .hide();
            //     _modal.find('.view_history_btn')
            //         .removeAttr('mv-errid')
            //         .hide();
            // }
            if(showChecked){
                _modal.find('.checked_btn')
                    .attr('mv-errid',$(this).attr('mv-errid'))
                    .show();
                showChecked = false;
            }
            _modal.find('.error-other-group').hide().find('#err_other').val('');
        })
        $("#history_modal").on('hide.bs.modal',function () {
            setTimeout(function () {
                $("#history_modal").find(".modal-body").empty();
            },1000);
        })
        $(".submitOverlayButton").click(function () {
            let _parent = $(this).parents('.__overlay');
            if(_parent.attr('mv-type') == 'submit'){
                car.polishSubmit($(_parent.attr('mv-el')), $('#__usercode').val())
            }else if(_parent.attr('mv-type') == 'recoat'){
                car.recoatCar($('#__usercode').val())
            }

        })
        <?php if($allowExport) : ?>
            setTimeout(function () {
                if(autoload) return true;
                car.checkDoneAll(true, true).then(r => {
                    console.log(r)
                });
            }, 500);
            setTimeout(function () {
                if($('body').find('.__loading').length == 0){
                    window.location.reload(true);
                }
            }, 3000);
        <?php endif; ?>
    })
</script>

</body>
</html>
