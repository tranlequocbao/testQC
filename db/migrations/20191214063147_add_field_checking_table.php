<?php

use Phinx\Migration\AbstractMigration;

class AddFieldCheckingTable extends AbstractMigration{
    public function change(){
        $table = $this->table('checking');
        $table->addColumn('err_id','string', ['default' => null])->save();
    }
}
