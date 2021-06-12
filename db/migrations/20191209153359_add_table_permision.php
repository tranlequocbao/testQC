<?php

use Phinx\Migration\AbstractMigration;

class AddTablePermision extends AbstractMigration{
    public function up(){
        $table = $this
            ->table('permision')
            ->addColumn('name', 'string')
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', "update" => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }

    public function down(){
        $table = $this->table('permision')->drop();
    }
}
