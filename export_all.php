<style>
    *{
        font-size: 18px;
    }
    input{
        padding: 7px 22px;
        width: 300px;
        margin-right: 10px;
    }
    button{
        margin: 20px 5px;
        padding: 4px 17px;
        cursor: pointer;
    }
    .d-none{
        display: none;
    }
    #table_data .item{
        padding: 7px 22px;
        font-size: 20px;
    }
    #table_data .item:first-child{
        margin-top: 10px;
    }
    #table_data .item:last-child{
        margin-bottom: 10px;
    }
</style>
<script src="./vendor/jquery/jquery.min.js"></script>
Từ ngày
<input type="date" id="start" value="<?=date('Y-m-d')?>">
Đến ngày
<input type="date" id="end" value="<?=date('Y-m-d')?>">

<div id="table_data" class="d-none"></div>
<div>
    <button id="get_data">Get data</button>
    <button id="export" class="d-none">Export</button>
</div>

<script>
    document.querySelector("#get_data").addEventListener('click', function(){
        var start = document.querySelector('#start').value;
        var end = document.querySelector('#end').value;

        $.ajax({
            url : './page/__getDataExportAll.php',
            type: 'POST',
            dataType: 'json',
            data : {
                start : start,
                end : end
            }, success : function(result){
                console.log(result)
                if(result.code != 200) return false;
                var html = '';
                for(let i in result.data){
                    html += '<div class="item">' + result.data[i] + '</div>';
                }
                $("#table_data").removeClass('d-none').append(html);
                $("#export").removeClass('d-none');
                $("#get_data").addClass('d-none');
                // window.location.href = './?all=1';
            }, error : function(error) {
                console.log(error.responseText)
            }
        })
    })
    $("#export").click(function(){
        window.location.href = './?all=1';
    })
</script>