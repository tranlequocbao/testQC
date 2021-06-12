$(document).ready(function(){
    $("body")
        .on('click','.loadPageArea',function(e){
            e.preventDefault();
            $('body').removeClass('sb-sidenav-toggled');
            admin.loadPage($(this).attr('page'),{'goto' : '1'});
        })
        .on('keypress', '.numberOnly', function (e) {
            return e.metaKey || //allow cmd/ctrl key
                    e.which <= 0 || //allow keys
                    e.which == 8 || //allow delete key
                /[0-9]/.test(String.fromCharCode(e.which));
        })
        .on('change', '#position', function (e) {
            let val = $(this).val();
            let location = $('body').find('#location');
            if(val == idSealer){
                $('body').find('.split_sealer').show(200);
            }else{
                $('body').find('.split_sealer').hide(200);
            }

            let allow = $('body').find('#position').find('option[value=' + val + ']').attr('strim');

            if(allow == '0'){
                location.find('option[dead=all]').hide();
                location.find('option[dead=one]').show();
                location.val(location.find('option[dead=one]:first').attr('value'));
            }else{
                location.find('option[dead=one]').hide();
                location.find('option[dead=all]').show();
                location.val(location.find('option[dead=all]:first').attr('value'));
            }

            if(val != RHID && val != LHID){
                location.removeAttr('readonly');
            }else{
                location.attr('readonly', true);
                location.val(val == RHID ? 'RH' : 'LH');
                $("#permision_create_error").prop('checked', true);
            }
        })
        .on('click', '.createUser, .editUser', function () {
            var form = $(this).closest('form');
            var get_empty = form.find('input[type="text"]').filter(function () {
                return !this.value;
            }).length;
            if(get_empty > 0){
                alert('Has an input empty!');
            }else{
                var data = $(this).closest('form').serializeArray();
                var split_sealer = $('body').find('#split_sealer').val();
                data.push({name : 'split_sealer', value : split_sealer});
                admin.save(data);
            }
            return false;
        })
        .on('change', '#permision_create_error', function () {
            if($(this).prop('checked')){
                $('body').find('#split_sealer').val('Create');
            }else{
                $('body').find('#split_sealer').val('Check');
            }
        })
        .on('mousedown', '.disableSelectInReadOnly', function (e) {
            if($(this).is('[readonly]')){
                e.preventDefault();
                this.blur();
                window.focus();
            }
        })
        .on('click','.goPage',function () {
            $page = $(this).attr('page');
            $goto = $(this).attr('goto');
            admin.loadPage($page,{'goto' : $goto});
            return false;
        })
        .on('click', '.editButton',function () {
            let _parent = $(this).closest('tr');
            let id = _parent.data('id');
            admin.loadPage('user-edit', {'id' : id});
        })
        .on('click', '.delButton', function () {
            let _parent = $(this).closest('tr');
            let id = _parent.data('id');
            alertify.confirm(
                "Are you sure delete this user?",
                function(){
                    admin.delUser(id);
                });


        })
    //end
})