<?php

use Phinx\Migration\AbstractMigration;

class AddFieldToTwoTableErrorHistoryAndChecking extends AbstractMigration{

    public function change(){
        $table = $this->table('checking');
        $table->addColumn('err_level', 'string', ['limit' => 4, 'default' => 1])->save();
        $table2 = $this->table('history_err');
        $table2->addColumn('err_level', 'string', ['limit' => 4, 'default' => 1])->save();
    }
}
