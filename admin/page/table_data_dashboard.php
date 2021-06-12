<div class="table-responsive">
    <table class="table table-bordered table-responsive center table-scrollbar" id="dataTable" width="100%" cellspacing="0" align="center">
        <thead>
        <tr>
            <th rowspan="4" class="w w-60">No</th>
            <th rowspan="4" class="w w-80">Model</th>
            <th rowspan="4" class="w w-180">Mã VIN</th>
            <th rowspan="4" class="w w-70">Màu/ Color</th>
            <th rowspan="4" class="w w-95">Loại lỗi/ Category defect</th>
            <th colspan="11">Vị trí lỗi trên body</th>
            <th rowspan="4" class="w w-74">Bên trong/ In side</th>
            <th rowspan="4" class="w w-65">Tổng lỗi</th>
        </tr>
        <tr>
            <th rowspan="3" class="w w-80">Ca bô/ bonnet</th>
            <th rowspan="3" class="w w-70">Mui/ roof panel</th>
            <th rowspan="3" class="w w-70">Cốp/ trunk lid/ lift gate</th>
            <th colspan="4">LH</th>
            <th colspan="4">RH</th>
        </tr>
        <tr>
            <th rowspan="2" class="w w-51">Gò má LH</th>
            <th rowspan="2" class="w w-70">Cửa trước LH</th>
            <th rowspan="2" class="w w-60">Cửa sau LH</th>
            <th rowspan="2" class="w w-70">Mảng hông LH</th>
            <th rowspan="2" class="w w-51">Gò má RH</th>
            <th rowspan="2" class="w w-70">Cửa trước RH</th>
            <th rowspan="2" class="w w-60">Cửa sau RH</th>
            <th rowspan="2" class="w w-70">Mảng hông RH</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($datas as $item => $data) : ?>
            <tr>
                <?php
                $roofLH = $data['MuiLH'] ?? 0;
                $roofRH = $data['MuiRH'] ?? 0;
                $roof = $roofLH + $roofRH;
                $roof = $roof == 0 ? '' :  $roof;
                ?>
                <td><?=$data['no']?></td>
                <td><?=$data['model']?></td>
                <td><?=$data['vin_code']?></td>
                <td><?=$data['color']?></td>
                <td><?=$data['category_defect']?></td>
                <td><?=$data['Capo'] ?? ''?></td>
                <td><?=$roof?></td>
                <td><?=$data['Ngoaicop'] ?? ''?></td>
                <td><?=$data['GomaLH'] ?? ''?></td>
                <td><?=$data['CuatruocLH'] ?? ''?></td>
                <td><?=$data['CuasauLH'] ?? ''?></td>
                <td><?=$data['ManghongLH'] ?? ''?></td>
                <td><?=$data['GomaRH'] ?? ''?></td>
                <td><?=$data['CuatruocRH'] ?? ''?></td>
                <td><?=$data['CuasauRH'] ?? ''?></td>
                <td><?=$data['ManghongRH'] ?? ''?></td>
                <td></td>
                <td><?=$data['sum'] ?? 0?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>