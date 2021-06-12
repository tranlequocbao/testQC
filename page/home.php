<?php
    if(!isset($_SESSION)) session_start();
    if(isset($home_folder)){
        $url = $home_folder;
    }
    else {
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') == 0) {
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/';
        } else {
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
        }
    }

    require_once "./vendor/PDOconnect/pdo/PdoConnect.php";
    $db = new pdoRequest();

    $aryUser = ['LH', 'RH', 'SEALER'];
    $aryLoadCarSealered = ['LH', 'RH'];
    $aryShowBtnNote = ['SEALER', 'RH', 'LH'];

    $showBtnNote = in_array(strtoupper($_SESSION['logined']['position']), $aryShowBtnNote);
    $showBtnSubmitError = $_SESSION['logined']['position'] == 'SEALER' && $_SESSION['logined']['split'] == 'CHECK' ? true : false;
    $showBtnSubmitCode = $_SESSION['logined']['position'] == 'SEALER' && $_SESSION['logined']['split'] == 'CHECK' ? false : true;
?>


<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>//</title>
    <meta name="_level" content="<?=strtoupper($_SESSION['logined']['split']) == 'CHECK' ? '2' : '3'?>">
    <link rel="shortcut icon" href="<?=$url . 'assets/images/favicon.png'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/bootstrap/css/bootstrap.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/owl_carousel/css/owl.carousel.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/owl_carousel/css/owl.theme.default.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'vendor/FontAwesome_4.7/css/font-awesome.min.css'?>">
    <link rel="stylesheet" href="<?=$url . 'assets/css/home.css'?>">
    <link rel="stylesheet" href="<?=$url . 'assets/css/home.css'?>">
</head>
<body id="_body">

    <div class="showHeader" style="display: none" onclick="$('#header').toggleClass('top');"></div>

    <section id="header" class="mb-4">
        <div class="container">
            <div class="row pt-3 pb-3">
                <div class="col-8">
                    <div class="form-group m-0 form_input_code d-flex w-100">
                    <?php if(isset($_GET['code_user'])) : ?>
                        <input type="text" class="form-control" id="car_code" placeholder="Car code" value="<?=$_GET['code_user']?>" readonly>
                    <?php else :
                        $code = 'vin_code';
                        if($_SESSION['logined']['position'] == 'SEALER'){
                            $db->setTable("rfid");
                            $code_vin = $db->get(['submited', '0'], null, ['date' => 'ASC']);
                        }
                        elseif(isset($_SESSION['logined']['user_check']) && $_SESSION['logined']['user_check'] == 'polishing'){
                            $sql = 'SELECT DISTINCT(error_code) as vin_code FROM `checking`  ORDER BY `created_at` DESC';
                            $code_vin = $db->_exec($sql);
                        }
                        else{
                            if(in_array($_SESSION['logined']['position'],$aryLoadCarSealered)){
                                $db->setTable("car_sealered");
                            }else{
                                $db->setTable("polish_car");
                            }
                            $code_vin = $db->get(['car_delete_flag', 0], 100, ['updated_at' => 'DESC']);
                        }
                        echo '<input type="text" class="form-control" id="vin-car-change" placeholder="Car code" value="" >';
                        echo '<div class="select-group scroll-custom" id="vincode_select_user">';


                        foreach ($code_vin as $item => $value){
                            if(!in_array($_SESSION['logined']['position'], $aryUser)) {
                                echo "<div class='select-item all' data-val='" . $value[$code] . "'>" . $value[$code] . "</div>";
                            }else{
                                echo "<div class='select-item one' data-val='" . $value[$code] . "'>" . $value[$code] . "</div>";
                            }

                        }


                        echo "</div>";
                    ?>
                    <?php endif; ?>
                    <?php if(strtoupper($_SESSION['logined']['position']) == "SEALER") : ?>
                        <?php if ($showBtnSubmitCode) : ?>
                            <button class="btn btn-primary ml-2 submitCodeSealer" onclick="car.submitCodeSealer()" style="display: none">Submit</button>
                        <?php endif; ?>
                    <?php else : ?>
                        <button class="btn btn-info ml-2 viewSealer" style="display: none">Sealer</button>
                    <?php endif; ?>
                    <?php if($showBtnNote) : ?>
                        <button class="btn btn-success ml-2 showOvlAddNote" onclick="car.showOvlAddNote()" style="display: none">Note</button>
                    <?php endif; ?>
                    </div>
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
    <section id="bodyer">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-10">
                    <div class="car_area __box_shadow"></div>
                </div>
            </div>
        </div>
    </section>
    <section id="footer"></section>

    <!--  Modal  -->
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
                        <select name="error_type" id="error_type" class="form-control" onchange="$(this).removeAttr('style');"></select>
                    </div>
                    <div class="form-group error-other-group" style="display: none">
                        <label for="err_other">Lỗi cụ thể</label>
                        <textarea name="err_other" id="err_other" class="form-control" rows="3" readonly onfocus="$(this).removeAttr('style')"></textarea>
                    </div>
                    <div class="form-group note-group" style="display: none">
                        <label for="note_this_error">Ghi chú</label>
                        <textarea name="note_this_error" id="note_this_error" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary delete d-none" onclick="car.submit($(this))">Delete</button>
                    <button class="btn btn-success checked_btn" onclick="car.doneError($(this),null,true)" style="display: none">Checked</button>
                    <button type="button" class="btn btn-primary submit" mv-type="add" onclick="car.submit($(this))">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_note" tabindex="-1" role="dialog" aria-labelledby="modal_note" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_error_title">Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group note-group">
                        <label for="note_car">Ghi chú</label>
                        <textarea name="note_car" id="note_car" class="form-control" rows="3" placeholder='Write Note For This Car'></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="car.modalNoteButtonSave(this)">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?=$url . 'vendor/jquery/jquery.min.js'?>"></script>
	<script src="<?=$url . 'vendor/bootstrap/js/popper.min.js'?>"></script>
	<script src="<?=$url . 'vendor/bootstrap/js/bootstrap.min.js'?>"></script>
	<script src="<?=$url . 'vendor/owl_carousel/js/owl.carousel.js'?>"></script>
    <script src="<?=$url . 'assets/js/translate.js'?>"></script>
    <script src="<?=$url . 'assets/js/car.js'?>"></script>
    <script src="<?=$url . 'assets/js/home.js'?>"></script>
    <script src="<?=$url . 'assets/js/err_json_sealer.js'?>"></script>
    <script src="<?=$url . 'assets/js/err_json.js'?>"></script>
	
    <script>
        var length_car_vin = 9;
        var length_car_code = 17;
        var focus_in = false;
        var noImportError = false;
        var position = '<?=$_SESSION['logined']['position']?>';
        var out_pos = '<?=$_SESSION['logined']['user_check'] ?? null?>';
        var err_option = position == 'SEALER' ? err_option_sealer : err_option_qc1k;
        var err_option_other_id = position == 'SEALER' ? err_option_sealer_other_id : err_option_qc1k_other_id;
        var isCreateError = '<?=$_SESSION['logined']['isCreateError'] == 1 ? '1' : '0'?>';
        var change_design = '<?=$_SESSION['logined']['username']?>';
    </script>
    <script>
        var elem = document.documentElement;
        function renderSelect() {
            let html = '';
            for(let i in err_option){
                html += "<option value='" + i + "'>" + err_option[i] + "</option>";
            }
            $("#error_type").empty().append(html);
        }
        $(document).ready(function () {
            renderSelect();
            $("#vincode_select_user").on('click', '.select-item', function(){
                $val = $(this).data('val');
                if($(this).hasClass('all')){
                    window.location.assign('./?code=' + $val);
                }else{
                    car.loadImg($val, true, position == 'SEALER');
                    car.setIn();
                    car.loadNoteSingle('#note_car', $val);
                    car.fullScreen();
                    $("#vin-car-change").val($val).attr('disabled', 'true');
                }
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
            $(document).keyup(function(e) {
                if (e.keyCode === 27){
                    $('#header').toggleClass('top');
                }
            });
            $(".viewSealer").click(function () {
                if($(this).hasClass('sealer')){
                    car.loadImg($val, true, false);
                    $(this).removeClass('sealer');
                    $(this).text('Sealer');
                    $(".submitErrorSealer").hide();
                    err_option = err_option_qc1k;
                    err_option_other_id = err_option_qc1k_other_id;
                    noImportError = false;
                    renderSelect();
                }else{
                    car.loadImg($val, true, true);
                    $(this).addClass('sealer');
                    $(this).text('QC1K');
                    $(".submitErrorSealer").show();
                    err_option = err_option_sealer;
                    err_option_other_id = err_option_sealer_other_id;
                    noImportError = true;
                    renderSelect();
                }
                car.setIn();
                car.fullScreen();
            })
            $("#modal_error").on('change', '#error_type',function () {
                if($("#error_type").val() == err_option_other_id){
                    $("#modal_error").find('.error-other-group').show();
                }else{
                    $("#modal_error").find('.error-other-group').hide().find('#err_other').val('');
                }
            })
        })
    </script>
</body>
</html>
