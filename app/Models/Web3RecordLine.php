<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Web3Record;


class Web3RecordLine extends Model
{
    use HasFactory;
    protected $fillable = ['id','web3record_id','line','line_nr', 'abi', 'parameters', 'value', 'int_value', 'columns', 'type', 'style', 'details',  'is_active', 'is_live'];


    public function web3record()
    {
        return $this->belongsTo(Web3Record::class, 'id', 'web3record_id');
    }


}
/*
CREATE TABLE `web3_record_lines` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `web3record_id` int(11) NOT NULL,
  `line` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `line_nr` int(11) NOT NULL,
  `abi` text COLLATE utf8mb4_unicode_ci,
  `parameters` text COLLATE utf8mb4_unicode_ci,
  `value` text COLLATE utf8mb4_unicode_ci,
  `int_value` int(11) DEFAULT NULL,
  `columns` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL,
  `is_live` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=543 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
