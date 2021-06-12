<?php


use Phinx\Seed\AbstractSeed;

class AddUserAccount extends AbstractSeed{
    public function run(){
        $table = $this->table('admin_account');
        $arr_insert = [
            'username' => 'RH',
            'password' => sha1('1'),
            'position' => 'RH'
        ];
        $table->insert($arr_insert)->save();
        $arr_insert = [
            'username' => 'LH',
            'password' => sha1('1'),
            'position' => 'LH'
        ];
        $table->insert($arr_insert)->save();
    }
}
