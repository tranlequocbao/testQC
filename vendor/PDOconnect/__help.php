<?php
///Demo sử dụng

//require cái pdo
require_once "pdo/PdoConnect.php";

//thiết lập kết nối. Thông số kết nối đưa ra như bên dưới.
//biến $db cũng sẽ sử dụng cho mọi thao tác select, insert, update, delete
$db = new pdoRequest([
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'test_pdo',
    'username' => 'markandr',
    'password' => '',
    'table' => 'user'
]);

//Lưu ý. Bắt buộc trong table phải có 1 column là id
//query tạo id khi tạo bảng: id int(11) auto_increment primary key not null

//1. Select;
//1.1 select all
    $result = $db->all();
//1.2 select where
    //tham số truyền vào của get gồm 2 cái. 1 là where, 2 là limit (limit là giới hạn lấy bao nhiêu row với những toán tử >, <, >=, <=, <>)
    //nếu chỉ where 1 chỗ thì mình sẽ dùng 1 mảng. nếu where nhiều chỗ phải là mảng chứa mảng.
    //1.2.1 demo where 1 chỗ (id)
    $result = $db->get(['id', 1]);
    //1.2.2 demo where 1 chỗ (id) không limit
    $result = $db->get(['id', '>', 2]);
    //1.2.3 demo where 1 chỗ (id) có limit 3 bản ghi
    $result = $db->get(['id', '>', 2], 3);
    //1.2.4 demo where nhiều chỗ (id, username)
    $result = $db->get([
        ['id', '>', 2],
        ['username', '=', 'admin']
    ]);
//1.3 select 1 row
    //select 1 row cũng gần giống với select where.
    $result = $db->one(['id', 1]);
//1.4 select theo page
//giả sử mình muốn mỗi trang sẽ có 10 row. mình muốn lấy những row ở trang 2
    $result = $db->getPage(2,10);
//giả sử mình muốn mỗi trang sẽ có 8 row. mình muốn lấy những row ở trang 3
    $result = $db->getPage(3,8);

//ghi chú
//$result = $db->get(['id', 1]); => hàm này thì cũng trả về 1 row nhưng dưới dạng $result = [ [id => 1, username = 'admin'] ]; tức là mảng trong mảng.
//$result = $db->one(['id', 1]); => hàm này trả về 1 row và 1 mảng dưới dạng $result = [id => 1, username = 'admin']; tức là dùng luôn mà không cần foreach

//2. insert
    //mảng $arr_insert sẽ là mảng chứa các giá trị cần insert
    $arr_insert = [
        'username' => 'abc',
        'password' => '1234'
    ];
    //insert sẽ có 3 giá trị. trong đó giá trị đầu tiên là mảng các giá trị cần inser
    //Giá trị thứ 2 là $duplicate là cột muốn kiểm tra đã tồn tại (ví dụ: username). Nếu muốn insert kể cả đã tồn tại thì bỏ qua giá trị này
    //Giá trị thứ 3 là $update là tùy chọn xem có muốn update lại row khi mà đã tồn tại không. (tức là nếu username đã tồn tại thì update).
    //2.1 insert thường (đã tồn tại vẫn insert)
    $result = $db->insert($arr_insert);
    //2.2 insert kiểm tra trùng và KHÔNG update nếu đã tồn tại. $result = false nếu như trùng.
    $result = $db->insert($arr_insert, 'username');
    //2.3 insert có kiểm tra trùng và sẽ update nếu như trùng
    $result = $db->insert($arr_insert, 'username', true);

//3. update
    //update thì chỉ có 1 mảng và where cần update
    //mảng $arr_update sẽ là mảng chứa các giá trị cần insert
    $arr_update = [
        'username' => 'abc',
        'password' => '1234'
    ];
    $result = $db->update($arr_insert, ['id', 10]);

//4. delete
    //delete thì chỉ có where cần delete
    $result = $db->delete(['id', 10]);