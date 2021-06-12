<div class="form-group" style="cursor: pointer">
    <input type="checkbox" id="viewAll" style="cursor: pointer" <?=isset($datas['all']) && $datas['all'] == '1' ? 'checked' : ''?>>
    <label for="viewAll" style="cursor: pointer">View all record</label>
</div>
<h5>View <?=count($datas['datas'])?> in <?=$datas['allRecord']?> result!</h5>
<table class="table m-0">
    <thead class="thead-light">
        <tr>
            <th scope="col" class="text-center">No</th>
            <th scope="col" class="text-center">Vin Code</th>
            <th scope="col" class="text-center">Body Type</th>
            <th scope="col" class="text-center">Color</th>
            <th scope="col" class="text-center">Count Error</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col" class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php $count = 1; foreach($datas['datas'] as $key => $data) : ?>
        <tr>
            <th scope="row" class="text-center"><?=$count++?></th>
            <td class="text-center"><?=$data['vin_code'] . $data['type_car']?></td>
            <td class="text-center"><?=$data['folder']?></td>
            <td class="text-center"><?=$data['color']?></td>
            <td class="text-center"><?=$data['sum_error']?></td>
            <td class="text-center"><?=$data['status']?></td>
            <td class="text-center">
                <a class="btn <?=$data['btn'] ?? 'btn-danger'?> exportThisCar" target="_blank" href="../export/?code_user=<?=$data['vin_code']?>">Export</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>