<?php

use Phinx\Migration\AbstractMigration;

class AddAdminAccountTable extends AbstractMigration{

    public function up(){
        $table = $this->table('admin_account');
        $table
            ->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('token', 'string', ['default' => 'null'])
            ->addColumn('position', 'string', ['default' => 'null'])
            ->addColumn('change_password', 'integer', ['default' => 0])
            ->addColumn('group_permision', "string", ['default' => 'null'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', "update" => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }

    public function down(){
        //no thing
    }
}
