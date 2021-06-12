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

    require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
    $db = new pdoRequest();

    $length_car_vin = 9;
    $length_car_code = 17;

    echo "<script>length_car_vin = " . $length_car_vin . "; length_car_code = " . $length_car_code . ";</script>";

    if(!is_null($userCode)){
        //get folder image
        $min_code = substr($userCode, 0, $length_car_vin);
        $db->setTable('car_code');
        $result = $db->one(['car_code', $min_code]);
        $folder = $result['car_folder'];
        //check recoat
        $db->setTable('checking');
        $carDelete = $db->one([['error_code', $userCode], ['recoat_flag','1']]);
        $carDelete = !empty($carDelete) ? 1 : 0;

        $aryErrorSealer = $aryErrorQc1k = [];
        $recoat_flag = '0';
        if($carDelete && (isset($_GET['viewHistory']) && $_GET['viewHistory'] == '1')){
            $recoat_flag = '1';
        }
        //get error sealer
        $db->setTable('sealer_checking');
        $aryErrorSealer = $db->get(['error_code', $userCode]);
        //get error qc1k
        $db->setTable('checking');
        $aryErrorQc1k = $db->get([['error_code', $userCode], ['recoat_flag',$recoat_flag]]);
        //get Image Sealer
        $aryImageSealer = recursiveSearch("../assets/images/" . $folder . '/SEALER',"/^.*\.(jpg|jpeg|png)$/");
        $aryImageSealer = getName($aryImageSealer);
        $aryErrorSealer = _json_replace_new_line($aryErrorSealer, 'err_note');
//        echo '<pre>';
//        print_r($aryErrorSealer);
//        echo '</pre>';
//        die;
        //get Image QC1K
        $aryImageQc1k['LH'] = recursiveSearch("../assets/images/" . $folder . '/LH',"/^.*\.(jpg|jpeg|png)$/");
        $aryImageQc1k['RH'] = recursiveSearch("../assets/images/" . $folder . '/RH',"/^.*\.(jpg|jpeg|png)$/");
        $aryImageQc1k['LH'] = getName($aryImageQc1k['LH']);
        $aryImageQc1k['RH'] = getName($aryImageQc1k['RH']);

        // echo "<pre>";
        // print_r(json_encode($aryErrorSealer));
        // echo "</pre>";
        // exit();
    }

    $aryViewListError = ['EXPORT'];
    $userCheck = $_SESSION['logined']['user_check'] ?? '';
    $viewListError = !!in_array(strtoupper($userCheck),$aryViewListError);

?>


<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export</title>
    <link rel="shortcut icon" href="<?=$url . 'assets/images/favicon.png'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/bootstrap/css/bootstrap.min.css'?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=$url . 'assets/css/home.css'?>">
    <style>
        .error_point.done_lv_3:before{
            font-size: 120%!important;
        }
    </style>
    <style>
        .addNote textarea{
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
        }
        .addNote textarea:disabled{
            background: #f9f9f9;
        }
        @media (max-width: 767px){
            .addNote textarea{
                margin:50px 10px;
            }
        }
    </style>
    <?php if(!is_null($userCode)) : ?>
        <script>
            var aryErrorSealer = JSON.parse('<?=json_encode($aryErrorSealer)?>');
            var aryErrorQc1k = JSON.parse('<?=json_encode($aryErrorQc1k)?>');
        </script>
    <?php endif; ?>
</head>
<body>

<?php if($viewListError) : ?>
    <div id="_bg_errorConfig" class="d-none" onclick="$('.showErrorConfig').click()"></div>
    <div id="errorConfig" class="left">
        <div class="_close" onclick="$('.showErrorConfig').click()">
            <i class="fa fa-close"></i>
        </div>
        <div class="_list"><ul></ul></div>
    </div>
<?php endif; ?>

    <section id="header" class="mb-4">
        <?php if($viewListError) : ?>
            <div class="showErrorConfig" onclick="$('#errorConfig').toggleClass('left');$('#_bg_errorConfig').toggleClass('d-none');$('body').toggleClass('no-scroll')">
                <i class="fa fa-bars"></i>
            </div>
        <?php endif; ?>
        <div class="container">
            <div class="row pt-3 pb-3">
                <div class="col-8">
                    <div class="form-group m-0 form_input_code d-flex w-100">
                        <?php if(isset($_GET['code_user'])) : ?>
                            <input type="text" class="form-control" id="car_code" placeholder="Car code" value="<?=$_GET['code_user']?>" readonly>
                            <button class="btn btn-success setExport w-25 ml-3" onclick="car.checkDoneAll(true)">Export</button>
                            <?php if($carDelete) : ?>
                                <?php if(!isset($_GET['viewHistory']) || $_GET['viewHistory'] != '1') : ?>
                                    <a class="btn btn-primary viewHistoryCar w-25 ml-3" href="./?code_user=<?=$userCode?>&viewHistory=1">History</a>
                                <?php else: ?>
                                    <a class="btn btn-primary closeViewHistoryCar w-25 ml-3" href="./?code_user=<?=$userCode?>">Go back</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else :
                            $db->setTable("plan_vin");
                            $code = 'vin_code';
                            $code_vin = $db->all([$code]);
                            echo '<input type="text" class="form-control" id="vin-car-change" placeholder="Car code" value="" >';
                            echo '<div class="select-group scroll-custom" id="vincode_select_user">';
                            foreach ($code_vin as $item => $value){
                                echo "<div class='select-item' data-val='" . $value[$code] . "'>" . $value[$code] . "</div>";
                            }
                            echo "</div>";
                        endif; ?>
                    </div>
                </div>
                <div class="col-4 text-right">
                    <h4 class="d-inline-block m-0">Hi <a href="./"><?=$_SESSION['logined']['username']?></a>! </h4>
                    <a href="<?=$url . 'login/logout.php'?>" class="pl-2">Logout</a>
                    
                </div>
            </div>
        </div>
    </section>
    <section id="bodyer">
        <?php if(!is_null($userCode)) : ?> 
            <div class="container-fluid area-submit">
                <div class="row justify-content-center qc1k_area" style="border-bottom: 1px solid #ddd;">
                    <div class="col-6" style="border-right: 1px solid #ddd;">
                        <div class="row car_area car_area_lh">
                            <h2 class='text-center d-block w-100'>Khu vực LH</h2>
                            <?php foreach ($aryImageQc1k['LH'] as $item => $val) : ?>
                                <div class="__box_img col-md-6 col-12 mt-1 mb-1" data-oldclass="__box_img col-md-6 col-12 mt-1 mb-1">
                                    <div class="__title_img text-center">
                                        <h6 class="_translate" data-text="<?=$val['_name']?>"></h6>              
                                    </div>
                                    <div class="__img mt-1 mb-1">
                                        <img src="../assets/images/<?=$folder?>/LH/<?=$val['image']?>" alt="<?=$val['_name']?>" class="w-100 __box_shadow">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-6" style="border-left: 1px solid #ddd;">
                        <div class="row car_area car_area_rh">
                            <h2 class='text-center d-block w-100'>Khu vực RH</h2>
                            <?php foreach ($aryImageQc1k['RH'] as $item => $val) : ?>
                                <div class="__box_img col-md-6 col-12 mt-1 mb-1" data-oldclass="__box_img col-md-6 col-12 mt-1 mb-1">
                                    <div class="__title_img text-center">
                                        <h6 class="_translate" data-text="<?=$val['_name']?>"></h6>              
                                    </div>
                                    <div class="__img mt-1 mb-1">
                                        <img src="../assets/images/<?=$folder?>/RH/<?=$val['image']?>" alt="<?=$val['_name']?>" class="w-100 __box_shadow">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center sealer_area">
                    <div class="col-12">
                        <div class="row car_area car_area_qc1k pb-5">
                            <h2 class='text-center d-block w-100 mt-4'>Khu vực SEALER</h2>
                            <?php foreach ($aryImageSealer as $item => $val) : ?>
                                <div class="__box_img col-lg-3 col-md-4 col-sm-6 col-12 mt-1 mb-1" data-oldclass="__box_img col-lg-3 col-md-4 col-sm-6 col-12 mt-1 mb-1">
                                    <div class="__title_img text-center">
                                        <h6 class="_translate" data-text="<?=$val['_name']?>"></h6>              
                                    </div>
                                    <div class="__img mt-1 mb-1">
                                        <img src="../assets/images/<?=$folder?>/SEALER/<?=$val['image']?>" alt="<?=$val['_name']?>" class="w-100 __box_shadow">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php
                    $db->setTable('note_car');
                    $result = $db->one(['vin_code', $userCode]);
                ?>
                <div class="addNote">
                    <textarea rows='3' class="note_car" <?=is_null($result) ? '' : 'disabled'?> data-autoresize placeholder='Write Note For This Car'><?=$result['note'] ?? ''?></textarea>
                </div>
            </div>
        <?php endif; ?>
    </section>
    <section id="footer"></section>

    <!--  Modal Error -->

    <div class="modal fade" id="modal_error" tabindex="-1" role="dialog" aria-labelledby="modal_error" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_error_title">Thêm lỗi mới</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h6>Loại lỗi</h6>
                    </div>
                    <div class="form-group">
                        <select name="error_type" id="error_type" class="form-control" disabled>
                            <option value="-1"></option>
                        </select>
                    </div>
                    <div class="form-group error-other-group" style="display: none">
                        <label for="err_other">Lỗi cụ thể</label>
                        <textarea name="err_other" id="err_other" class="form-control" rows="3" readonly onfocus="$(this).removeAttr('style')"></textarea>
                    </div>
                    <div class="form-group note-group" style="display: none">
                        <label for="note_this_error">Ghi chú</label>
                        <textarea name="note_this_error" id="note_this_error" class="form-control" rows="3" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary viewHistory" data-dismiss="modal">History</button>
                    <button type="button" class="btn btn-secondary closeButtonModal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--  Modal History -->
    <div class="modal fade" id="modal_history" tabindex="-1" role="dialog" aria-labelledby="modal_history" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_history_title">History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>body</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?=$url . 'vendor/jquery/jquery.min.js'?>"></script>
	<script src="<?=$url . 'vendor/bootstrap/js/popper.min.js'?>"></script>
    <script src="<?=$url . 'vendor/bootstrap/js/bootstrap.min.js'?>"></script>  
    <script src="<?=$url . 'vendor/html2canvas/html2canvas.min.js'?>"></script>
    <script src="<?=$url . 'assets/js/translate.js'?>"></script>
    <script src="<?=$url . 'assets/js/err_json_sealer.js'?>"></script>
    <script src="<?=$url . 'assets/js/err_json.js'?>"></script>
    <script src="<?=$url . 'assets/js/car.js'?>"></script>
    <script>
        var length_car_vin = 9;
        var length_car_code = 17;
        var focus_in = false;
        var noImportError = false;
        var position = '<?=$_SESSION['logined']['position']?>';
        var out_pos = '<?=$_SESSION['logined']['user_check'] ?? null?>';
        var _modal = $("#modal_error");
        var _modal_history = $("#modal_history");
        var err_option = position == 'SEALER' ? err_option_sealer : err_option_qc1k;
        
    </script>
    <script>
        $(document).ready(function(){
            function renderErrorList(){
                let html = '';
                //QC1K
                var keys = Object.keys(err_option_qc1k);
                html += '<h3 class="pl-3">QC1K</h3>';
                for(let i = 0; i < keys.length; i++){
                    html += "<li>" + keys[i] + " : " + err_option_qc1k[keys[i]] + "</li>";
                }
                //SEALER
                keys = Object.keys(err_option_sealer);
                html += '<h3 class="pl-3 mt-4">SEALER</h3>';
                for(let i = 0; i < keys.length; i++){
                    html += "<li>" + keys[i] + " : " + err_option_sealer[keys[i]] + "</li>";
                }
                $("#errorConfig ._list ul").empty().append(html);
            }
            <?php if($viewListError) : ?>
                renderErrorList();
            <?php endif; ?>
            $("#vincode_select_user").on('click', '.select-item', function(){
                $val = $(this).data('val');
                window.location.assign('./?code_user=' + $val);
            })
            $("#vin-car-change").on('keyup',function () {
                let val = $(this).val().trim();
                let item = $("#vincode_select_user").find(".select-item");
                //reset select
                item.removeClass('hidden');
                //set select with input
                item.each(function () {
                    if($(this).text().indexOf(val.toUpperCase()) == -1){
                        $(this).addClass('hidden');
                    }
                })
            })
            <?php if(!is_null($userCode)) :  ?>
                $("#bodyer").find("._translate").each(function(){
                    let val = $(this).data('text');
                    $(this).text(car.translate(val));
                })
                car.cbLoadError({data : aryErrorSealer}, ".sealer_area", '<?=$folder?>', true);
                car.cbLoadError({data : aryErrorQc1k}, ".qc1k_area", '<?=$folder?>');

                $(".container-fluid").find(".error_point").each(function(){
                    let text = $(this).attr('mv-error');
                    let topThis = parseFloat($(this).css('top'));
                    let html = '';
                    if(topThis < 34){
                        html = '<span style="font-size: 120%;position: absolute;top: 100%;left: 0px;font-weight: bold;line-height: 1;padding: 2px 4px;background: #fff;">'+text+'</span>'
                    }
                    else html = '<span style="font-size: 120%;position: absolute;bottom: 100%;left: 0px;font-weight: bold;line-height: 1;padding: 2px 4px;background: #fff;">'+text+'</span>';
                    $(this).empty().append(html);
                })
            <?php endif; ?>
            $(".error_point").click(function () {
                let _this = $(this);
                let error_type = _this.attr('mv-error') !== undefined ? _this.attr('mv-error') : '';
                let err_option = _this.hasClass('sealer') ? err_option_sealer : err_option_qc1k;
                let err_id = _this.attr('mv-errid');
                let err_other = _this.attr('mv-errother');

                if(err_other == 'null'){
                    err_other = 'Thông tin chưa được ghi chép';
                }

                $("#error_type").empty();
                for(let i in err_option){
                    let html = "<option value='" + i + "'>" + err_option[i] + "</option>";
                    $("#error_type").append(html);
                }

                let note = _this.attr('mv-errnote');
                if(note == 'null'){
                    note =  'Chưa có ghi chú!';
                }
                if(typeof note != 'undefined' && note != null && note != ''){
                    _modal.find(".note-group").show().find('#note_this_error').val(note).show();
                }
                _modal.find('#modal_error_title').text('View');
                _modal.find('select').val(error_type);
                _modal.find('.viewHistory').attr('mv-errid',err_id);
                if(
                    ($(this).hasClass('sealer') && error_type == err_option_sealer_other_id)
                    || !$(this).hasClass('sealer') && error_type == err_option_qc1k_other_id
                ){
                    _modal.find('.error-other-group').show().find('#err_other').val(err_other);
                }else{
                    _modal.find('.error-other-group').hide().find('#err_other').val('');
                }

                $user = $(this).hasClass('sealer') ? 'SEALER' : 'QC1K';
                $("#modal_history").attr('mv-user',$user);

                _modal.modal('show');
            })
            _modal.on('hide.bs.modal',function () {
                _modal.find(".note-group").hide()
                return true;
            })
            $(".viewHistory").click(function () {
                let err_id = $(this).attr('mv-errid');
                car.viewHistory($("#modal_history"),err_id);
            })
        })
    </script>
    <script>
        jQuery.each(jQuery('textarea[data-autoresize]'), function() {
            var offset = this.offsetHeight - this.clientHeight;
            var resizeTextarea = function(el) {
                jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
            };
            resizeTextarea(this);
            jQuery(this).on('keyup input', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
        });
    </script>

</body>
</html>
