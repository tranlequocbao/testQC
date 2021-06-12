<?php
foreach ($position as $t => $a){
    if(strtoupper($a['position']) == 'SEALER'){
        $needle = $a['id'];
    }
    if(strtoupper($a['position']) == 'RH'){
        $RHID = $a['id'];
    }if(strtoupper($a['position']) == 'LH'){
        $LHID = $a['id'];
    }
}

?>
<script>
    var idSealer = '<?=$needle?>';
    var RHID = '<?=$RHID?>';
    var LHID = '<?=$LHID?>';
</script>
<div class="container-fluid mt-5">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#" class="loadPageArea" page="dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#" class="loadPageArea" page="user">User</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Edit User <?=$_user['usercode']?>
        </div>
        <div class="card-body">
            <form action="Hehe. No Action" method="POST">
                <input type="hidden" name="_id" value="<?=$_user['id']?>">
                <input type="hidden" name="usercode" value="<?=$_user['usercode']?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="usercode">User Code</label>
                        <input type="text" class="form-control" id="usercode" value="<?=$_user['usercode']?>" disabled>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" id="fullname" placeholder="Ful name" value="<?=$_user['fullname']?>" name="fullname">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="position">Position</label>
                        <select name="position" id="position" class="form-control">
                            <?php foreach ($position as $item => $val) : ?>
                                <option value="<?=$val['id']?>" <?=$_user['position'] == $val['id'] ? 'selected' : ''?> strim="<?=$val['allowAll']?>"><?=strtoupper($val['position'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="location">Location</label>
                        <select name="location" id="location" class="form-control disableSelectInReadOnly" location="<?=$_user['location']?>">
                            <option value="RH" dead="one">RH</option>
                            <option value="LH" dead="one">LH</option>
                            <option value="ALL" dead="all">ALL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="create_error">Allow Create Error</label>
                        <div class="customCheckbox" id="create_error">
                            <input type="checkbox" <?=$_user['permision_create_error'] == '1' ? 'checked' : ''?> class="d-none form-control" id="permision_create_error" name="permision_create_error">
                            <label for="permision_create_error"></label>
                        </div>
                    </div>
                </div>
                <div class="form-row split_sealer" <?=$needle == $_user['position'] ? '' : 'style="display: none"'?>>
                    <div class="form-group col-md-12">
                        <label for="split_sealer">Split Sealer</label>
                        <select name="split_sealer" id="split_sealer" class="form-control w-25" disabled>
                            <option value="Create" <?=$_user['split_sealer'] != 'Check' && $_user['permision_create_error'] == '1' ? 'selected' : ''?>>Create</option>
                            <option value="Check" <?=$_user['split_sealer'] == 'Check' || $_user['permision_create_error'] == '0' ? 'selected' : ''?>>Check</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary editUser">Save edit</button>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var location = $('#location').attr('location') || 'ALL';
        $('#location').val(location);
        if(location == 'RH' || location == 'LH'){
            $('#location').find('option[value=ALL]').hide();
        }else{
            $('#location').find('option[value=LH]').hide();
            $('#location').find('option[value=RH]').hide();
        }
        var val = $("#position").val();
        if(val == RHID || val == LHID){
            $('#location').attr('readonly', true);
            $('#location').val(val == RHID ? 'RH' : 'LH');
            $("#permision_create_error").prop('checked', true).change();
        }
    })
</script>