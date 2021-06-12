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
        <li class="breadcrumb-item active">Add</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Add User
        </div>
        <div class="card-body">
            <form action="Hehe. No Action" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="usercode">User Code</label>
                        <input type="text" class="form-control numberOnly" id="usercode" placeholder="User code" name="usercode">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" id="fullname" placeholder="Ful name" name="fullname">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="position">Position</label>
                        <select name="position" id="position" class="form-control">
                            <?php foreach ($position as $item => $val) : ?>
                                <option value="<?=$val['id']?>" strim="<?=$val['allowAll']?>"><?=strtoupper($val['position'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="location">Location</label>
                        <select name="location" id="location" class="form-control disableSelectInReadOnly" readonly="">
                            <option value="RH" dead="one">RH</option>
                            <option value="LH" dead="one">LH</option>
                            <option value="ALL" dead="all">ALL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="create_error">Allow Create Error</label>
                        <div class="customCheckbox" id="create_error">
                            <input type="checkbox" class="d-none form-control" id="permision_create_error" name="permision_create_error" checked>
                            <label for="permision_create_error"></label>
                        </div>
                    </div>
                </div>
                <div class="form-row split_sealer" style="display: none">
                    <div class="form-group col-md-12">
                        <label for="split_sealer">Split Sealer</label>
                        <select name="split_sealer" id="split_sealer" class="form-control w-25" readonly>
                            <option value="Create">Create</option>
                            <option value="Check" selected>Check</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary createUser">Create</button>
            </form>
        </div>
    </div>
</div>