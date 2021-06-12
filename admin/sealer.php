<style>
    .badge{
        margin-left: 3px;
    }
</style>
<div class="container-fluid">
    <div class="card mb-4 mt-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Detail Vincode
            <button class="btn btn-primary float-right exportData" sort="{}">Export <img src="./assets/img/download.svg" alt="download icon" style="width: 25px;margin-top: -5px"></button>
            <div class="select_date float-right mr-3">
                <input type="text" name="start_date" <?=isset($datas['all']) && $datas['all'] == '1' ? 'disabled' : ''?> class="form-control d-inline-block datepicker date-sort" style="width: 150px;" placeholder="Start Date" value="<?=(isset($datas['startTime']) && $datas['startTime'] != '') ? $datas['startTime'] : date('m/d/Y')?>">
                <span class="ml-2 mr-2">~</span>
                <input type="text" name="end_date" <?=isset($datas['all']) && $datas['all'] == '1' ? 'disabled' : ''?> class="form-control d-inline-block datepicker date-sort" style="width: 150px;" placeholder="End Date" value="<?=(isset($datas['endTime']) && $datas['endTime'] != '') ? $datas['endTime'] : date('m/d/Y')?>">
            </div>
        </div>
        <div class="card-body" id="table_detail">
            <?php require_once __DIR__ . '/page/table_data_detail.php'?>
        </div>
    </div>
    <div class="pagination-area" id="pagination">
        <?php require_once __DIR__ . '/page/pagging_detail.php'?>
    </div>
</div>
<script>
    $(document).ready(function(){
        $(".datepicker").datepicker({
            autoclose: true,
            todayHighlight: true,
            format : 'mm/dd/yyyy'
        })
        $(".exportData").attr('sort', JSON.stringify({
            start : '<?=date('m/d/Y')?>',
            end: '<?=date('m/d/Y')?>'
        }))
        $("#viewAll").on('change', function(){
            var checkedFlag = $(this).prop('checked');
            if(checkedFlag && !confirm("Show all?")){
                return false;
            }
            var optionSort = {
                all : checkedFlag ? '1' : '0',
                p : '1'
            };
            if(!checkedFlag){
                let start = $('input[name=start_date]').val().trim();
                let end = $('input[name=end_date]').val().trim();
                optionSort = {
                    start : start,
                    end: end
                };
            }
            $(".exportData").attr('sort', JSON.stringify(optionSort))
            admin.sortDetailTable(optionSort);
            return false;
        })
        $('input[name=start_date]').on('change', function(){
            if($(this).val() == '') return false;
            $('input[name=end_date]').val(''); return false;
        })
        $('input[name=end_date]').on('change', function(){
            if(
                $(this).val().trim() == ''
                || $('input[name=start_date]').val().trim() == ''
            ){
                return false;
            }

            let start = $('input[name=start_date]').val().trim();
            let end = $('input[name=end_date]').val().trim();
            optionSort = {
                start : start,
                end: end,
                p : '1'
            };
            $(".exportData").attr('sort', JSON.stringify(optionSort))
            admin.sortDetailTable(optionSort);
        })
        $("#pagination").on('click', '.setGotoPage', function(e){
            e.preventDefault();
            e.stopPropagation();
            var p = $(this).attr('data-p');
            var options = {
                p : p
            };
            let start = $('input[name=start_date]').val().trim();
            let end = $('input[name=end_date]').val().trim();
            var checkedFlag = $("#viewAll").prop('checked');
            if(start !== '' && end !== ''){
                options['start'] = start;
                options['end'] = end;
            }
            options['all'] = checkedFlag ? '1' : '0';
            $(".exportData").attr('sort', JSON.stringify(options))
            admin.sortDetailTable(options);
            return false;
        })
        $(".exportData").click(function () {
            var sortData = $(this).attr('sort');
            sortData = JSON.parse(sortData);
            admin.exportDataDetail(sortData);
        })
    })
</script>