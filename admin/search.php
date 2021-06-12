<div class="container-fluid">
<ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#" class="loadPageArea" page="dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Search</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
           
            <button class="btn btn-primary float-right exportData" sort="{}">Export <img src="./assets/img/download.svg" alt="download icon" style="width: 25px;margin-top: -5px"></button>
            <!-- <div class="select_date float-right mr-3">
                <input type="text" name="start_date" class="form-control d-inline-block datepicker date-sort" style="width: 150px;" placeholder="Start Date">
                <span class="ml-2 mr-2">~</span>
                <input type="text" name="end_date" class="form-control d-inline-block datepicker date-sort" style="width: 150px;" placeholder="End Date">
            </div> -->
        </div>
        <div class="card-body" id="table_dashboard">
            <?php require_once __DIR__ . '/page/table_data_dashboard.php'?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $(".datepicker").datepicker({
            autoclose: true,
            todayHighlight: true,
            format : 'mm/dd/yyyy'
        })
        $('table').find('th.w, td.w').each(function () {
            let cls = $(this).attr('class').split(/\s+/);
            for(let i in cls){
                if(cls[i].indexOf('w-') == 0){
                    let extrac = cls[i].split('w-');
                    extrac = extrac[extrac.length - 1];
                    $(this).css({
                        'width' : extrac + 'px',
                        'min-width' : extrac + 'px',
                        'max-width' : extrac + 'px'
                    })
                    break;
                }
            }
        })
        var scroll = false;
        var mouseX = 0;
        var left = $('table#dataTable').scrollLeft();
        $('#table_dashboard')
            .on('mousedown','table#dataTable',function (event) {
                scroll = true;
                mouseX = event.clientX;
                left = $('table#dataTable').scrollLeft();
                $('table#dataTable').addClass('drag')
                $('table#dataTable').on('mouseup',function (event) {
                    scroll = false;
                    $('table#dataTable').removeClass('drag')
                })
                $('table#dataTable').on('mousemove',function (event) {
                    if(!scroll) return false;
                    var x = event.clientX;
                    left = left - x + mouseX;
                    $('table#dataTable').scrollLeft(left);
                    mouseX = x;
                })
            })
            .on('mouseleave',function (event) {
                scroll = false;
                $('table#dataTable').removeClass('drag')
            })
        $(".date-sort").on('change', function(){
            var _this = $(this);
            var _els = null;
            let nameThis = $(this).attr('name');
            if(nameThis == 'start_date'){
                _els = $('input[name=end_date]');
            }else{
                _els = $('input[name=start_date]');
            }
            if(_els.val().trim() == '' || _this.val().trim() == ''){
                return false;
            }
            let start = $('input[name=start_date]').val().trim();
            let end = $('input[name=end_date]').val().trim();
            var optionSort = {
                start : start,
                end: end
            };
            $(".exportData").attr('sort', JSON.stringify(optionSort))
            admin.sortDashboardTable(optionSort);
        })
        $(".exportData").click(function () {
            var sortData = $(this).attr('sort');
            sortData = JSON.parse(sortData);
            admin.exportData(sortData);
        })
    })
</script>