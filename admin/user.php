<div class="container-fluid mt-5">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#" class="loadPageArea" page="dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">User</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Dashboard User
            <a href="" class="float-right loadPageArea" page="user-add">Add user</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Usercode</th>
                        <th>Fullname</th>
                        <th>Position</th>
                        <th>Per_create</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($user as $i => $u) : ?>
                        <tr class="<?=$u['position'] == 'ADMIN' ? 'adminControl' : ''?>" data-id="<?=$u['id']?>">
                            <td><?=$u['usercode']?></td>
                            <td><?=$u['fullname']?></td>
                            <td><?=$u['position']?></td>
                            <td>
                                <div class="customCheckbox">
                                    <input type="checkbox" <?=$u['permision_create_error'] ? 'checked' : ''?> id="per_create_<?=$i?>" class="d-none" disabled>
                                    <label for="per_create_<?=$i?>"></label>
                                </div>
                            </td>
                            <td><?= is_null($u['location']) || $u['location'] == '' ? 'ALL' : $u['location'] ?></td>
                            <td>
                                <button class="btn btn-primary editButton">Edit</button>
                                <button class="btn btn-danger delButton">Del</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <ul class="pagination">
                <li class="page-item <?=$goto == '1' ? 'disabled' : ''?>"><a class="page-link goPage" page="user" goto="<?=(int)$goto-1?>" href="#">Previous</a></li>
                <?php for ($i = 1; $i <= ceil($allRow / $countRow); $i++) : ?>
                    <li class="page-item <?=$i == $goto ? 'disabled' : ''?>"><a class="page-link goPage" page="user" goto="<?=(int)$i?>" href="#"><?=$i?></a></li>
                <?php endfor; ?>
                <li class="page-item <?=$goto == ceil($allRow / $countRow) ? 'disabled' : ''?>"><a class="page-link goPage" page="user" goto="<?=(int)$goto+1?>" href="#">Next</a></li>
            </ul>
        </div>

    </div>
</div>

<script>
    var callClear = setInterval(function () {
        if(typeof admin != 'undefined'){
            admin.clearAdminRow('.adminControl');
            clearInterval(callClear);
        }
    },100);
</script>