<?php

use Phinx\Seed\AbstractSeed;

class AddAdminAccount extends AbstractSeed{
    public function run(){
        $table = $this->table('admin_account');
        $arr_insert = [
            'username' => 'admin',
            'password' => sha1('1')
        ];
        $table->insert($arr_insert)->save();
    }
}
