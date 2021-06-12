function AdminController() {
    var self = this;
    var path = './';
    var loading = false;
    var mainArea = $("#layoutSidenav_content main");
    this.ajax = (url, data, succFunc, doneFunc) => {
        $.ajax({
            url : path + url,
            type : 'POST',
            dataType : 'json',
            data : data,
            success : function(result){
                succFunc(result);
            },
            error : function(error) {
                console.log(error.responseText);
            },
            complete : function () {
                if(typeof doneFunc != 'undefined'){
                    doneFunc();
                }else{
                    loading = false;
                }
            }
        })
    }
    this.loadPage = (page, params = {}, el = '.loadPageArea') =>{
        if(loading){
            alertify.warning('There is another action being taken');
            return false;
        }
        loading = true;
        self.ajax(
            'load.php',
            {'page' : page, 'params' : params},
            function(result){
                $(".loadPageArea").removeClass('active');
                $(el + '[page=' + page + ']').addClass('active');
                mainArea.empty().append(result.content);
            },
            function(){
                loading = false;
            });
    }
    this.clearAdminRow = (el) => {
        $(el).find('a').removeAttr('href')
        $(el).find('button').addClass('disabled').attr('disabled', 'true')
        $(el).find('input').addClass('disabled').attr('disabled', 'true')
    }
    this.save = (data) => {
        if(loading){
            alertify.warning('There is another action being taken');
            return false;
        }
        loading = true;
        self.ajax(
            'page/user_add_edit_ajax.php',
            {'data' : data},
            function(result){
                if(result.code == 200){
                    loading = false;
                    self.loadPage('user');
                    alertify.success(result.message);
                    return true;
                }else if(result.code == 201){
                    $("#layoutSidenav_content").find('#usercode').val('').focus();
                }
                alertify.error(result.message);
            },
            function(){
                loading = false;
            });
    }
    this.delUser = (id) => {
        if(loading){
            alertify.warning('There is another action being taken');
            return false;
        }
        loading = true;
        self.ajax(
            'page/user_add_edit_ajax.php',
            {id : id, _type : 'del'},
            function (result) {
                if(result.code == 200){
                    alertify.success(result.message);
                    self.delRow(id);
                }else{
                    alertify.error(result.message)
                }
            })
    }
    this.delRow = (id) => {
        var findRow = $('#layoutSidenav_content').find('tr[data-id=' + id + ']');
        if(findRow.length > 0){
            findRow.remove();
        }else{
            alertify.error('We try remove row of id ' + id + ' but could not find!')
        }
    }
    this.sortDashboardTable = (options = null) =>{
        if(options == null || typeof options != 'object'){
            return false;
        }
        self.ajax(
            'page/sortDashboard.php',
            {options : options},
            function(result){
                alertify.success('Sort Success')
                $("body").find('#table_dashboard').empty().append(result.html);
            }
        );
    }
    this.sortDetailTable = (options = null) =>{
        if(options == null || typeof options != 'object'){
            return false;
        }
        self.ajax(
            'page/sortDetail.php',
            {options : options},
            function(result){
                alertify.success('Sort Success')
                $("body").find('main').empty().append(result.html);
            }
        );
    }
    this.exportData = (options = {}) => {
        alertify.confirm(
            'Confirm Export',
            'Are you sure export data?',
            function () {
                self.cbExportData(options);
            }, function () {
                return false;
            });
    }
    this.cbExportData = (options = {}) => {
        self.ajax(
            'page/exportData.php',
            {options : options},
            function(result){
                console.log(result);
                if(result.code == 200){
                    window.location.href = window.location.href + '../' + result.url;
                }
            }
        );
    }
    this.exportDataDetail = (options = {}) => {
        alertify.confirm(
            'Confirm Export',
            'Are you sure export data?',
            function () {
                self.cbExportDataDetail(options);
            }, function () {
                return false;
            });
    }
    this.cbExportDataDetail = (options = {}) => {
        self.ajax(
            'page/exportDataDetail.php',
            {options : options},
            function(result){
                console.log(result);
                if(result.code == 200){
                    var optionSave = {
                        'page' : 'detail',
                        'sort' : options
                    }
                    window.location.href = window.location.href + '../' + result.url;
                }
            }
        );
    }
}
var admin = new AdminController();
