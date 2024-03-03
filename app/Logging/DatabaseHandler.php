<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Handler\AbstractProcessingHandler;
use DB;

class DatabaseHandler extends AbstractProcessingHandler {

    protected function write(LogRecord $record): void {
        $data = $record->toArray();

        DB::table('logs')->insert([
            'message' => $data['message'],
            'context' => json_encode($data['context']),
            'level' => $data['level'],
            'level_name' => $data['level_name'],
            'channel' => $data['channel'],
            'extra' => json_encode($data['extra']),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

}
