<?php

use Phinx\Migration\AbstractMigration;

class CreateCheckingTable extends AbstractMigration{

    public function change(){
        $table = $this->table('checking');
        $table->addColumn('error_code','string')
            ->addColumn('error_type','string')
            ->addColumn('error_position','string')
            ->addColumn('error_toadoX','string')
            ->addColumn('error_toadoY','string')
            ->addColumn('error_user','string')
            ->addColumn('updated_at','timestamp',['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_at','timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }
}
