<?php

use Phinx\Migration\AbstractMigration;

class CreateCarCodeTable extends AbstractMigration{
    public function change(){
        $table = $this->table('car_code');
        $table->addColumn('car_code','string')
            ->addColumn('car_folder', 'string')
            ->addColumn('updated_at','timestamp',['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_at','timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }
}
