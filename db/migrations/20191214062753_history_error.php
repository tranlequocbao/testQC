<?php

use Phinx\Migration\AbstractMigration;

class HistoryError extends AbstractMigration{
    public function change(){
        $table = $this->table('history_err');
        $table
            ->addColumn('err_id', 'string', ['limit' => 25, 'default' => null])
            ->addColumn('err_user_change', 'string')
            ->addColumn('err_time_change','string')
            ->addColumn('err_date_change','string')
            ->addColumn('updated_at', 'timestamp',['default' => 'CURRENT_TIMESTAMP', 'update' => "CURRENT_TIMESTAMP"])
            ->addColumn('created_at', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }
}
